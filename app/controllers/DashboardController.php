<?php

namespace app\controllers;

use app\models\BesoinModel;
use app\models\DonModel;
use app\models\DistributionModel;
use app\models\VilleModel;
use flight\Engine;

class DashboardController
{
    protected Engine $app;
    protected VilleModel $villeModel;
    protected BesoinModel $besoinModel;
    protected DonModel $donModel;
    protected DistributionModel $distributionModel;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->villeModel = new VilleModel($app);
        $this->besoinModel = new BesoinModel($app);
        $this->donModel = new DonModel($app);
        $this->distributionModel = new DistributionModel($app);
    }

    /**
     * Afficher le tableau de bord avec les KPIs et l'état des villes
     */
    public function index(): void
    {
        // KPIs
        $villes = $this->villeModel->getAll();
        $besoins = $this->besoinModel->getAllWithVille();
        $dons = $this->donModel->getAll();

        $total_villes = count($villes);
        $total_besoins = count($besoins);
        $total_dons = count($dons);

        // Calcul du taux de satisfaction global
        $total_quantite_besoins = 0;
        $total_quantite_distribuee = 0;

        foreach ($besoins as $besoin) {
            $total_quantite_besoins += $besoin['quantite'];
        }

        $distributions = $this->distributionModel->getAllWithDetails();
        foreach ($distributions as $dist) {
            $total_quantite_distribuee += $dist['quantite_attribuee'];
        }

        $taux_satisfaction = $total_quantite_besoins > 0
            ? round(($total_quantite_distribuee / $total_quantite_besoins) * 100) . '%'
            : '0%';

        $stats = [
            'total_villes' => $total_villes,
            'total_besoins' => $total_besoins,
            'total_dons' => $total_dons,
            'taux_satisfaction' => $taux_satisfaction,
        ];

        // Détails par ville
        $villes_stats = [];
        foreach ($villes as $ville) {
            $dist_ville = $this->distributionModel->getDistributionsParVille($ville['id']);
            $total_dons_recus = $dist_ville['total_recus'] ?? 0;

            // Calculer le total des besoins pour cette ville
            $total_besoins_ville = 0;
            $besoins_list_parts = [];
            foreach ($besoins as $besoin) {
                if ($besoin['ville_id'] === $ville['id']) {
                    $total_besoins_ville += $besoin['quantite'];
                    $besoins_list_parts[] = $besoin['description'] . ' (' . $besoin['quantite'] . ')';
                }
            }
            $besoins_list = implode(', ', $besoins_list_parts);

            // Déterminer le statut
            if ($total_besoins_ville === 0) {
                $status_text = 'Aucun besoin';
                $status_class = 'text-muted';
            } elseif ($total_dons_recus >= $total_besoins_ville) {
                $status_text = 'Satisfait';
                $status_class = 'status-satisfait';
            } elseif ($total_dons_recus > 0) {
                $status_text = 'Partiel';
                $status_class = 'status-partiel';
            } else {
                $status_text = 'Non satisfait';
                $status_class = 'status-non';
            }

            $villes_stats[] = [
                'nom' => $ville['nom'],
                'region' => $ville['region_nom'] ?? '',
                'besoins_list' => $besoins_list ?: 'Aucun besoin',
                'total_dons_recus' => $total_dons_recus,
                'status_text' => $status_text,
                'status_class' => $status_class,
            ];
        }

        $this->app->render('pages/dashboard', [
            'stats' => $stats,
            'villes_stats' => $villes_stats,
        ]);
    }
}
