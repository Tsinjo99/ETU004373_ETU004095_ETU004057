<?php

namespace app\controllers;

use app\models\BesoinModel;
use app\models\DonModel;
use app\models\DistributionModel;
use flight\Engine;

class DispatchController
{
    protected Engine $app;
    protected BesoinModel $besoinModel;
    protected DonModel $donModel;
    protected DistributionModel $distributionModel;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->besoinModel = new BesoinModel($app);
        $this->donModel = new DonModel($app);
        $this->distributionModel = new DistributionModel($app);
    }

    /**
     * Afficher la page du dispatch avec les dernières distributions
     */
    public function index(): void
    {
        $distributions = $this->distributionModel->getAllWithDetails();

        $this->app->render('pages/dispatch', [
            'distributions' => $distributions,
            'simulation' => null,
            'message' => $this->app->get('flash_message'),
            'error' => $this->app->get('flash_error'),
        ]);
    }

    /**
     * Calculer les distributions sans les enregistrer (simulation)
     */
    private function calculerDistributions(): array
    {
        $besoins = $this->besoinModel->getNonSatisfaits();
        $dons = $this->donModel->getNonDistribues();

        $distributions_prevues = [];

        // Copier les quantités restantes en mémoire
        $dons_restants = [];
        foreach ($dons as $don) {
            $dons_restants[$don['id']] = (int) $don['quantite_restante'];
        }

        $besoins_restants = [];
        foreach ($besoins as $besoin) {
            $besoins_restants[$besoin['id']] = (int) $besoin['quantite_restante'];
        }

        // Indexer les besoins par ID pour accès rapide
        $besoins_index = [];
        foreach ($besoins as $besoin) {
            $besoins_index[$besoin['id']] = $besoin;
        }

        // Algorithme de dispatch
        foreach ($dons as $don) {
            if ($dons_restants[$don['id']] <= 0) {
                continue;
            }

            foreach ($besoins as $besoin) {
                if ($besoins_restants[$besoin['id']] <= 0) {
                    continue;
                }

                // Matching par type ET description (insensible à la casse)
                if (
                    $don['type_don'] === $besoin['type_besoin']
                    && mb_strtolower(trim($don['description'])) === mb_strtolower(trim($besoin['description']))
                ) {
                    $quantite_a_attribuer = min(
                        $dons_restants[$don['id']],
                        $besoins_restants[$besoin['id']]
                    );

                    if ($quantite_a_attribuer > 0) {
                        $distributions_prevues[] = [
                            'besoin_id' => $besoin['id'],
                            'don_id' => $don['id'],
                            'quantite' => $quantite_a_attribuer,
                            'besoin_desc' => $besoin['description'],
                            'don_desc' => $don['description'],
                            'type' => $don['type_don'],
                            'ville_nom' => $besoin['ville_nom'] ?? 'N/A',
                        ];

                        $dons_restants[$don['id']] -= $quantite_a_attribuer;
                        $besoins_restants[$besoin['id']] -= $quantite_a_attribuer;
                    }
                }

                if ($dons_restants[$don['id']] <= 0) {
                    break;
                }
            }
        }

        return $distributions_prevues;
    }

    /**
     * Simuler le dispatch (aperçu sans enregistrement)
     */
    public function simulate(): void
    {
        $distributions_prevues = $this->calculerDistributions();
        $distributions = $this->distributionModel->getAllWithDetails();

        $this->app->render('pages/dispatch', [
            'distributions' => $distributions,
            'simulation' => $distributions_prevues,
            'message' => count($distributions_prevues) > 0 
                ? 'Simulation: ' . count($distributions_prevues) . ' distribution(s) possible(s). Cliquez sur Valider pour confirmer.'
                : 'Aucune distribution possible.',
            'error' => null,
        ]);
    }

    /**
     * Valider et exécuter le dispatch (enregistrement réel)
     */
    public function run(): void
    {
        $distributions_prevues = $this->calculerDistributions();
        $nb_distributions = 0;

        foreach ($distributions_prevues as $dist) {
            $this->distributionModel->create(
                $dist['besoin_id'],
                $dist['don_id'],
                $dist['quantite']
            );
            $nb_distributions++;
        }

        if ($nb_distributions > 0) {
            $this->app->set('flash_message', $nb_distributions . ' distribution(s) créée(s) avec succès !');
        } else {
            $this->app->set('flash_message', 'Aucune nouvelle distribution possible.');
        }

        $this->app->redirect('/dispatch');
    }
}
