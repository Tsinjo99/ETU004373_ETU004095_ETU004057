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
            'message' => $this->app->get('flash_message'),
            'error' => $this->app->get('flash_error'),
        ]);
    }

    /**
     * Exécuter l'algorithme de dispatch
     * - Trier besoins par date ASC
     * - Trier dons par date ASC
     * - Matcher par type ET description
     * - Attribuer MIN(don.restante, besoin.restante)
     */
    public function run(): void
    {
        $besoins = $this->besoinModel->getNonSatisfaits();
        $dons = $this->donModel->getNonDistribues();

        $nb_distributions = 0;

        // Copier les quantités restantes en mémoire pour manipulation
        $dons_restants = [];
        foreach ($dons as $don) {
            $dons_restants[$don['id']] = (int) $don['quantite_restante'];
        }

        $besoins_restants = [];
        foreach ($besoins as $besoin) {
            $besoins_restants[$besoin['id']] = (int) $besoin['quantite_restante'];
        }

        // Algorithme de dispatch : pour chaque don, chercher des besoins correspondants
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
                        $this->distributionModel->create(
                            $besoin['id'],
                            $don['id'],
                            $quantite_a_attribuer
                        );

                        $dons_restants[$don['id']] -= $quantite_a_attribuer;
                        $besoins_restants[$besoin['id']] -= $quantite_a_attribuer;
                        $nb_distributions++;
                    }
                }

                // Si le don est épuisé, passer au suivant
                if ($dons_restants[$don['id']] <= 0) {
                    break;
                }
            }
        }

        if ($nb_distributions > 0) {
            $this->app->set('flash_message', $nb_distributions . ' distribution(s) créée(s) avec succès !');
        } else {
            $this->app->set('flash_message', 'Aucune nouvelle distribution possible (pas de correspondance type/description, ou tout est déjà distribué).');
        }

        $this->app->redirect('/dispatch');
    }
}
