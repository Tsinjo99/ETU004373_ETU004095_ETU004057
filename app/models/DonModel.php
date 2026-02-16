<?php

namespace app\models;

use flight\Engine;

class DonModel
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    /**
     * Récupérer tous les dons
     */
    public function getAll(): array
    {
        $statement = $this->app->db()->prepare('SELECT * FROM dons ORDER BY date_don DESC');
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Créer un nouveau don
     */
    public function create(string $type_don, string $description, int $quantite): bool
    {
        $statement = $this->app->db()->prepare(
            'INSERT INTO dons (type_don, description, quantite) VALUES (?, ?, ?)'
        );
        return $statement->execute([$type_don, $description, $quantite]);
    }

    /**
     * Récupérer les dons non entièrement distribués (quantité restante > 0)
     * Calcul dynamique via la table distributions
     */
    public function getNonDistribues(): array
    {
        $statement = $this->app->db()->prepare(
            'SELECT dons.*, 
                    (dons.quantite - COALESCE(SUM(distributions.quantite_attribuee), 0)) AS quantite_restante
             FROM dons
             LEFT JOIN distributions ON dons.id = distributions.don_id
             GROUP BY dons.id
             HAVING quantite_restante > 0
             ORDER BY dons.date_don ASC'
        );
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Supprimer un don
     */
    public function delete(int $id): bool
    {
        $statement = $this->app->db()->prepare('DELETE FROM dons WHERE id = ?');
        return $statement->execute([$id]);
    }
}
