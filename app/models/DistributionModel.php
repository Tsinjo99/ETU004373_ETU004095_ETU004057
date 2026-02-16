<?php

namespace app\models;

use flight\Engine;

class DistributionModel
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    /**
     * Créer une distribution (attribution d'un don à un besoin)
     */
    public function create(int $besoin_id, int $don_id, int $quantite_attribuee): bool
    {
        $statement = $this->app->db()->prepare(
            'INSERT INTO distributions (besoin_id, don_id, quantite_attribuee) VALUES (?, ?, ?)'
        );
        return $statement->execute([$besoin_id, $don_id, $quantite_attribuee]);
    }

    /**
     * Récupérer toutes les distributions avec détails
     */
    public function getAllWithDetails(): array
    {
        $statement = $this->app->db()->prepare(
            'SELECT distributions.*, 
                    besoins.description AS besoin_desc,
                    besoins.type_besoin,
                    dons.description AS don_desc,
                    dons.type_don,
                    villes.nom AS ville_nom
             FROM distributions
             JOIN besoins ON distributions.besoin_id = besoins.id
             JOIN dons ON distributions.don_id = dons.id
             JOIN villes ON besoins.ville_id = villes.id
             ORDER BY distributions.date_distribution DESC'
        );
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Total des dons reçus par ville
     */
    public function getDistributionsParVille(int $ville_id): array
    {
        $statement = $this->app->db()->prepare(
            'SELECT COALESCE(SUM(distributions.quantite_attribuee), 0) AS total_recus
             FROM distributions
             JOIN besoins ON distributions.besoin_id = besoins.id
             WHERE besoins.ville_id = ?'
        );
        $statement->execute([$ville_id]);
        return $statement->fetch();
    }

    /**
     * Supprimer toutes les distributions (reset)
     */
    public function deleteAll(): bool
    {
        $statement = $this->app->db()->prepare('DELETE FROM distributions');
        return $statement->execute();
    }
}
