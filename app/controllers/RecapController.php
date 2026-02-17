<?php
// app/controllers/RecapController.php

namespace app\controllers;

use flight\Engine;

class RecapController
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    /**
     * Page de récapitulation
     */
    public function index(): void
    {
        $data = $this->calculateRecap();
        $this->app->render('pages/recap', $data);
    }

    /**
     * Endpoint JSON pour Ajax
     */
    public function data(): void
    {
        $data = $this->calculateRecap();
        $this->app->json($data);
    }

    /**
     * Calcul des données de récapitulation
     */
    private function calculateRecap(): array
    {
        $db = $this->app->db();
        
        // Total des besoins (quantite * prix_unitaire)
        $besoinsTotal = $db->fetchRow(
            "SELECT COALESCE(SUM(quantite * prix_unitaire), 0) as montant FROM besoins"
        );
        
        // Total satisfait par distributions (quantite_attribuee * prix_unitaire du besoin)
        $distribTotal = $db->fetchRow(
            "SELECT COALESCE(SUM(d.quantite_attribuee * b.prix_unitaire), 0) as montant 
             FROM distributions d 
             JOIN besoins b ON d.besoin_id = b.id"
        );
        
        // Total satisfait par achats (montant_total)
        $achatsTotal = $db->fetchRow(
            "SELECT COALESCE(SUM(montant_total), 0) as montant FROM achats"
        );
        
        // Total des dons argent disponibles
        $donsArgentTotal = $db->fetchRow(
            "SELECT COALESCE(SUM(quantite), 0) as montant FROM dons WHERE type_don = 'argent'"
        );
        
        // Total dons argent utilisés (achats)
        $donsArgentUtilises = $db->fetchRow(
            "SELECT COALESCE(SUM(montant_total), 0) as montant FROM achats"
        );
        
        // Détails par ville
        $parVille = $db->fetchAll(
            "SELECT 
                v.id,
                v.nom as ville_nom,
                COALESCE(SUM(b.quantite * b.prix_unitaire), 0) as besoins_montant,
                COALESCE((
                    SELECT SUM(d.quantite_attribuee * b2.prix_unitaire)
                    FROM distributions d
                    JOIN besoins b2 ON d.besoin_id = b2.id
                    WHERE b2.ville_id = v.id
                ), 0) as distribue_montant,
                COALESCE((
                    SELECT SUM(a.montant_total)
                    FROM achats a
                    JOIN besoins b3 ON a.besoin_id = b3.id
                    WHERE b3.ville_id = v.id
                ), 0) as achats_montant
             FROM villes v
             LEFT JOIN besoins b ON b.ville_id = v.id
             GROUP BY v.id, v.nom
             ORDER BY v.nom"
        );
        
        // Calcul des restants par ville
        foreach ($parVille as &$ville) {
            $ville['satisfait_montant'] = $ville['distribue_montant'] + $ville['achats_montant'];
            $ville['restant_montant'] = $ville['besoins_montant'] - $ville['satisfait_montant'];
            $ville['pourcentage'] = $ville['besoins_montant'] > 0 
                ? round(($ville['satisfait_montant'] / $ville['besoins_montant']) * 100, 1) 
                : 0;
        }
        unset($ville);
        
        // Totaux globaux
        $satisfaitTotal = $distribTotal['montant'] + $achatsTotal['montant'];
        $restantTotal = $besoinsTotal['montant'] - $satisfaitTotal;
        $pourcentageGlobal = $besoinsTotal['montant'] > 0 
            ? round(($satisfaitTotal / $besoinsTotal['montant']) * 100, 1) 
            : 0;
        
        return [
            'besoins_total' => $besoinsTotal['montant'],
            'distributions_total' => $distribTotal['montant'],
            'achats_total' => $achatsTotal['montant'],
            'satisfait_total' => $satisfaitTotal,
            'restant_total' => $restantTotal,
            'pourcentage_global' => $pourcentageGlobal,
            'dons_argent_total' => $donsArgentTotal['montant'],
            'dons_argent_utilises' => $donsArgentUtilises['montant'],
            'dons_argent_restant' => $donsArgentTotal['montant'] - $donsArgentUtilises['montant'],
            'par_ville' => $parVille,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}
