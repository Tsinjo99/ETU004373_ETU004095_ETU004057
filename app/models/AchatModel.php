<?php

namespace app\models;

use flight\Engine;

class AchatModel
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    /**
     * Créer un achat (achat de besoin nature/materiaux via don argent)
     */
    public function create(int $besoin_id, int $don_id, int $quantite, float $montant_ht, float $frais_pourcent, float $montant_total): bool
    {
        $statement = $this->app->db()->prepare(
            'INSERT INTO achats (besoin_id, don_id, quantite_achetee, montant_ht, frais_pourcent, montant_total) 
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        return $statement->execute([$besoin_id, $don_id, $quantite, $montant_ht, $frais_pourcent, $montant_total]);
    }

    /**
     * Récupérer tous les achats avec détails
     */
    public function getAllWithDetails(): array
    {
        $statement = $this->app->db()->prepare(
            'SELECT achats.*, 
                    besoins.description AS besoin_desc,
                    besoins.type_besoin,
                    besoins.prix_unitaire,
                    villes.id AS ville_id,
                    villes.nom AS ville_nom
             FROM achats
             JOIN besoins ON achats.besoin_id = besoins.id
             JOIN villes ON besoins.ville_id = villes.id
             ORDER BY achats.date_achat DESC'
        );
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Récupérer les achats par ville
     */
    public function getByVille(int $ville_id): array
    {
        $statement = $this->app->db()->prepare(
            'SELECT achats.*, 
                    besoins.description AS besoin_desc,
                    besoins.type_besoin,
                    besoins.prix_unitaire,
                    villes.nom AS ville_nom
             FROM achats
             JOIN besoins ON achats.besoin_id = besoins.id
             JOIN villes ON besoins.ville_id = villes.id
             WHERE villes.id = ?
             ORDER BY achats.date_achat DESC'
        );
        $statement->execute([$ville_id]);
        return $statement->fetchAll();
    }

    /**
     * Récupérer le total des achats par besoin
     */
    public function getTotalAchatsByBesoin(int $besoin_id): int
    {
        $statement = $this->app->db()->prepare(
            'SELECT COALESCE(SUM(quantite_achetee), 0) AS total 
             FROM achats 
             WHERE besoin_id = ?'
        );
        $statement->execute([$besoin_id]);
        $result = $statement->fetch();
        return (int) $result['total'];
    }

    /**
     * Récupérer le total des montants dépensés par don argent
     */
    public function getTotalDepenseByDon(int $don_id): float
    {
        $statement = $this->app->db()->prepare(
            'SELECT COALESCE(SUM(montant_total), 0) AS total 
             FROM achats 
             WHERE don_id = ?'
        );
        $statement->execute([$don_id]);
        $result = $statement->fetch();
        return (float) $result['total'];
    }

    /**
     * Supprimer tous les achats
     */
    public function deleteAll(): bool
    {
        return $this->app->db()->exec('DELETE FROM achats') !== false;
    }
}
