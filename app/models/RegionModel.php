<?php

namespace app\models;

use flight\Engine;

class RegionModel
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    /**
     * Récupérer toutes les régions
     */
    public function getAll(): array
    {
        $statement = $this->app->db()->prepare('SELECT * FROM regions ORDER BY nom');
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Récupérer une région par ID
     */
    public function getById(int $id): array|false
    {
        $statement = $this->app->db()->prepare('SELECT * FROM regions WHERE id = ?');
        $statement->execute([$id]);
        return $statement->fetch();
    }

    /**
     * Créer une nouvelle région
     */
    public function create(string $nom): bool
    {
        $statement = $this->app->db()->prepare('INSERT INTO regions (nom) VALUES (?)');
        return $statement->execute([$nom]);
    }

    /**
     * Supprimer une région
     */
    public function delete(int $id): bool
    {
        $statement = $this->app->db()->prepare('DELETE FROM regions WHERE id = ?');
        return $statement->execute([$id]);
    }
}
