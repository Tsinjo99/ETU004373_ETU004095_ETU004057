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

    /**
     * Récupérer les dons en argent avec solde restant (après achats)
     * Calcul: quantite - somme des montants totaux des achats
     */
    public function getDonsArgentDisponibles(): array
    {
        $statement = $this->app->db()->prepare(
            "SELECT dons.*, 
                    (dons.quantite - COALESCE(SUM(achats.montant_total), 0)) AS solde_restant
             FROM dons
             LEFT JOIN achats ON dons.id = achats.don_id
             WHERE dons.type_don = 'argent'
             GROUP BY dons.id
             HAVING solde_restant > 0
             ORDER BY dons.date_don ASC"
        );
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Vérifier si un besoin existe encore dans les dons restants (même type + description)
     */
    public function besoinExisteDansDonsRestants(string $type_besoin, string $description): bool
    {
        $dons = $this->getNonDistribues();
        foreach ($dons as $don) {
            if ($don['type_don'] === $type_besoin && mb_strtolower(trim($don['description'])) === mb_strtolower(trim($description))) {
                return true;
            }
        }
        return false;
    }
}
