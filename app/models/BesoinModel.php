<?php

namespace app\models;

use flight\Engine;

class BesoinModel
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    /**
     * Récupérer tous les besoins avec le nom de la ville
     */
    public function getAllWithVille(): array
    {
        $statement = $this->app->db()->prepare(
            'SELECT besoins.*, villes.nom AS ville_nom 
             FROM besoins 
             JOIN villes ON besoins.ville_id = villes.id 
             ORDER BY besoins.date_besoin DESC'
        );
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Créer un nouveau besoin
     */
    public function create(int $ville_id, string $type_besoin, string $description, int $quantite, float $prix_unitaire): bool
    {
        $statement = $this->app->db()->prepare(
            'INSERT INTO besoins (ville_id, type_besoin, description, quantite, prix_unitaire) 
             VALUES (?, ?, ?, ?, ?)'
        );
        return $statement->execute([$ville_id, $type_besoin, $description, $quantite, $prix_unitaire]);
    }

    /**
     * Récupérer les besoins non satisfaits (quantité restante > 0)
     * Calcul dynamique via la table distributions
     */
    public function getNonSatisfaits(): array
    {
        $statement = $this->app->db()->prepare(
            'SELECT besoins.*, 
                    villes.nom AS ville_nom,
                    (besoins.quantite - COALESCE(SUM(distributions.quantite_attribuee), 0)) AS quantite_restante
             FROM besoins
             JOIN villes ON besoins.ville_id = villes.id
             LEFT JOIN distributions ON besoins.id = distributions.besoin_id
             GROUP BY besoins.id
             HAVING quantite_restante > 0
             ORDER BY besoins.date_besoin ASC'
        );
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Supprimer un besoin
     */
    public function delete(int $id): bool
    {
        $statement = $this->app->db()->prepare('DELETE FROM besoins WHERE id = ?');
        return $statement->execute([$id]);
    }
}
