<?php

namespace app\controllers;

use app\models\VilleModel;
use app\models\RegionModel;
use flight\Engine;

class VilleController
{
    protected Engine $app;
    protected VilleModel $villeModel;
    protected RegionModel $regionModel;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->villeModel = new VilleModel($app);
        $this->regionModel = new RegionModel($app);
    }

    /**
     * Afficher la liste des villes + formulaire d'ajout
     */
    public function index(): void
    {
        $villes = $this->villeModel->getAll();
        $regions = $this->regionModel->getAll();

        $this->app->render('pages/villes', [
            'villes' => $villes,
            'regions' => $regions,
            'message' => $this->app->get('flash_message'),
            'error' => $this->app->get('flash_error'),
        ]);
    }

    /**
     * Enregistrer une nouvelle ville
     */
    public function store(): void
    {
        $request = $this->app->request();
        $nom = trim($request->data->nom ?? '');
        $region_id = (int) $request->data->region_id;

        // Validation
        if (empty($nom) || $region_id <= 0) {
            $this->app->set('flash_error', 'Veuillez remplir tous les champs correctement.');
            $this->index();
            return;
        }

        $this->villeModel->create($nom, $region_id);
        $this->app->set('flash_message', 'Ville ajoutée avec succès !');
        $this->app->redirect('/villes');
    }

    /**
     * Supprimer une ville
     */
    public function delete(): void
    {
        $request = $this->app->request();
        $id = (int) $request->data->id;

        if ($id <= 0) {
            $this->app->set('flash_error', 'Ville invalide.');
            $this->app->redirect('/villes');
            return;
        }

        $this->villeModel->delete($id);
        $this->app->set('flash_message', 'Ville supprimée avec succès.');
        $this->app->redirect('/villes');
    }

    /**
     * Enregistrer une nouvelle région
     */
    public function storeRegion(): void
    {
        $request = $this->app->request();
        $nom = trim($request->data->nom ?? '');

        if (empty($nom)) {
            $this->app->set('flash_error', 'Le nom de la région est requis.');
            $this->app->redirect('/villes');
            return;
        }

        $this->regionModel->create($nom);
        $this->app->set('flash_message', 'Région ajoutée avec succès !');
        $this->app->redirect('/villes');
    }
}
