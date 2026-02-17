<?php

namespace app\controllers;

use app\models\AchatModel;
use app\models\BesoinModel;
use app\models\DonModel;
use app\models\VilleModel;
use flight\Engine;

class AchatController
{
    protected Engine $app;
    protected AchatModel $achatModel;
    protected BesoinModel $besoinModel;
    protected DonModel $donModel;
    protected VilleModel $villeModel;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->achatModel = new AchatModel($app);
        $this->besoinModel = new BesoinModel($app);
        $this->donModel = new DonModel($app);
        $this->villeModel = new VilleModel($app);
    }

    /**
     * Afficher la page des achats avec liste filtrable par ville
     */
    public function index(): void
    {
        $ville_id = $this->app->request()->query->ville_id ?? null;
        
        if ($ville_id) {
            $achats = $this->achatModel->getByVille((int) $ville_id);
        } else {
            $achats = $this->achatModel->getAllWithDetails();
        }
        
        $besoins_achetables = $this->besoinModel->getBesoinsAchetables();
        $dons_argent = $this->donModel->getDonsArgentDisponibles();
        $villes = $this->villeModel->getAll();
        
        // Récupérer le frais d'achat depuis la config
        $config = require __DIR__ . '/../config/config.php';
        $frais_achat = $config['frais_achat'] ?? 10;

        $this->app->render('pages/achats', [
            'achats' => $achats,
            'besoins_achetables' => $besoins_achetables,
            'dons_argent' => $dons_argent,
            'villes' => $villes,
            'ville_id_filter' => $ville_id,
            'frais_achat' => $frais_achat,
            'message' => $this->app->get('flash_message'),
            'error' => $this->app->get('flash_error'),
        ]);
    }

    /**
     * Effectuer un achat
     */
    public function store(): void
    {
        $request = $this->app->request();
        $besoin_id = (int) $request->data->besoin_id;
        $don_id = (int) $request->data->don_id;
        $quantite = (int) $request->data->quantite;

        // Validation de base
        if ($besoin_id <= 0 || $don_id <= 0 || $quantite <= 0) {
            $this->app->set('flash_error', 'Veuillez remplir tous les champs correctement.');
            $this->index();
            return;
        }

        // Récupérer le besoin
        $besoins = $this->besoinModel->getBesoinsAchetables();
        $besoin = null;
        foreach ($besoins as $b) {
            if ((int) $b['id'] === $besoin_id) {
                $besoin = $b;
                break;
            }
        }

        if (!$besoin) {
            $this->app->set('flash_error', 'Besoin non trouvé ou déjà satisfait.');
            $this->app->redirect('/achats');
            return;
        }

        // Vérifier si le besoin existe encore dans les dons restants
        if ($this->donModel->besoinExisteDansDonsRestants($besoin['type_besoin'], $besoin['description'])) {
            $this->app->set('flash_error', 'Erreur: Ce besoin existe encore dans les dons restants. Utilisez d\'abord le dispatch avant d\'acheter.');
            $this->app->redirect('/achats');
            return;
        }

        // Vérifier la quantité demandée
        if ($quantite > (int) $besoin['quantite_restante']) {
            $this->app->set('flash_error', 'La quantité demandée dépasse la quantité restante du besoin.');
            $this->app->redirect('/achats');
            return;
        }

        // Récupérer le don argent
        $dons_argent = $this->donModel->getDonsArgentDisponibles();
        $don = null;
        foreach ($dons_argent as $d) {
            if ((int) $d['id'] === $don_id) {
                $don = $d;
                break;
            }
        }

        if (!$don) {
            $this->app->set('flash_error', 'Don argent non trouvé ou solde insuffisant.');
            $this->app->redirect('/achats');
            return;
        }

        // Calculer le montant
        $config = require __DIR__ . '/../config/config.php';
        $frais_pourcent = $config['frais_achat'] ?? 10;
        
        $montant_ht = $quantite * (float) $besoin['prix_unitaire'];
        $montant_total = $montant_ht * (1 + $frais_pourcent / 100);

        // Vérifier le solde du don argent
        if ($montant_total > (float) $don['solde_restant']) {
            $this->app->set('flash_error', 'Solde insuffisant dans le don argent sélectionné. Montant requis: ' . number_format($montant_total, 2) . ' Ar');
            $this->app->redirect('/achats');
            return;
        }

        // Créer l'achat
        $this->achatModel->create($besoin_id, $don_id, $quantite, $montant_ht, $frais_pourcent, $montant_total);

        $this->app->set('flash_message', 'Achat effectué avec succès ! Montant: ' . number_format($montant_total, 2) . ' Ar (dont ' . $frais_pourcent . '% de frais)');
        $this->app->redirect('/achats');
    }
}
