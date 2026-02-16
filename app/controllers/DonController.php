<?php

namespace app\controllers;

use app\models\DonModel;
use flight\Engine;

class DonController
{
    protected Engine $app;
    protected DonModel $donModel;

    public function __construct(Engine $app)
    {
        $this->app = $app;
        $this->donModel = new DonModel($app);
    }

    /**
     * Afficher le formulaire + liste des dons
     */
    public function index(): void
    {
        $dons = $this->donModel->getAll();

        $this->app->render('pages/dons', [
            'dons' => $dons,
            'message' => $this->app->get('flash_message'),
            'error' => $this->app->get('flash_error'),
        ]);
    }

    /**
     * Enregistrer un nouveau don
     */
    public function store(): void
    {
        $request = $this->app->request();
        $type_don = $request->data->type_don;
        $description = trim($request->data->description ?? '');
        $quantite = (int) $request->data->quantite;

        // Validation
        if (empty($description) || $quantite <= 0) {
            $this->app->set('flash_error', 'Veuillez remplir tous les champs correctement.');
            $this->index();
            return;
        }

        $types_valides = ['nature', 'materiaux', 'argent'];
        if (!in_array($type_don, $types_valides, true)) {
            $this->app->set('flash_error', 'Type de don invalide.');
            $this->index();
            return;
        }

        $this->donModel->create($type_don, $description, $quantite);
        $this->app->set('flash_message', 'Don enregistré avec succès !');
        $this->app->redirect('/dons');
    }
}
