<?php

namespace app\models;

use flight\Engine;

class VilleModel
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    /**
     * Récupérer toutes les villes avec leur région
     */
    public function getAll(): array
    {
        $statement = $this->app->db()->prepare(
            'SELECT villes.*, regions.nom AS region_nom 
             FROM villes 
             LEFT JOIN regions ON villes.region_id = regions.id 
             ORDER BY villes.nom'
        );
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Récupérer une ville par ID
     */
    public function getById(int $id): array|false
    {
        $statement = $this->app->db()->prepare(
            'SELECT villes.*, regions.nom AS region_nom 
             FROM villes 
             LEFT JOIN regions ON villes.region_id = regions.id 
             WHERE villes.id = ?'
        );
        $statement->execute([$id]);
        return $statement->fetch();
    }

    /**
     * Créer une nouvelle ville
     */
    public function create(string $nom, int $region_id): bool
    {
        $statement = $this->app->db()->prepare(
            'INSERT INTO villes (nom, region_id) VALUES (?, ?)'
        );
        return $statement->execute([$nom, $region_id]);
    }

    /**
     * Supprimer une ville
     */
    public function delete(int $id): bool
    {
        $statement = $this->app->db()->prepare('DELETE FROM villes WHERE id = ?');
        return $statement->execute([$id]);
    }
}
