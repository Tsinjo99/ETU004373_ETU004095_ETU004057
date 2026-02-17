<?php

namespace app\controllers;

use app\models\BesoinModel;
use app\models\VilleModel;
use flight\Engine;

class BesoinController
{
    protected Engine $app;
    protected BesoinModel $besoinModel;
    protected VilleModel $villeModel;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->besoinModel = new BesoinModel($app);
        $this->villeModel = new VilleModel($app);
    }

    /**
     * Afficher le formulaire + liste des besoins
     */
    public function index(): void
    {
        $besoins = $this->besoinModel->getAllWithVille();
        $villes = $this->villeModel->getAll();

        $this->app->render('pages/besoins', [
            'besoins' => $besoins,
            'villes' => $villes,
            'message' => $this->app->get('flash_message'),
            'error' => $this->app->get('flash_error'),
        ]);
    }

    /**
     * Enregistrer un nouveau besoin
     */
    public function store(): void
    {
        $request = $this->app->request();
        $ville_id = (int) $request->data->ville_id;
        $type_besoin = $request->data->type_besoin;
        $description = trim($request->data->description ?? '');
        $quantite = (int) $request->data->quantite;
        $prix_unitaire = (float) $request->data->prix_unitaire;

        // Validation
        if ($ville_id <= 0 || empty($description) || $quantite <= 0 || $prix_unitaire < 0) {
            $this->app->set('flash_error', 'Veuillez remplir tous les champs correctement.');
            $this->index();
            return;
        }

        $types_valides = ['nature', 'materiaux', 'argent'];
        if (!in_array($type_besoin, $types_valides, true)) {
            $this->app->set('flash_error', 'Type de besoin invalide.');
            $this->index();
            return;
        }

        $this->besoinModel->create($ville_id, $type_besoin, $description, $quantite, $prix_unitaire);
        $this->app->set('flash_message', 'Besoin enregistré avec succès !');
        $this->app->redirect('/besoins');
    }
}
