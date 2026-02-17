<?php
// app/views/pages/recap.php
include __DIR__ . '/../layouts/header.php';
?>

<!-- Page Header -->
<div class="page-header animate__animated animate__fadeIn">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">
                <i class="bi bi-graph-up-arrow me-2"></i>Récapitulation
            </h1>
            <p class="page-subtitle">Vue d'ensemble des besoins et des distributions</p>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-primary btn-custom" id="refreshBtn">
                <i class="bi bi-arrow-clockwise me-2"></i>Actualiser
            </button>
        </div>
    </div>
</div>

<!-- Last Update -->
<div class="text-muted mb-4 animate__animated animate__fadeIn">
    <i class="bi bi-clock me-1"></i>
    Dernière mise à jour: <span id="lastUpdate"><?= htmlspecialchars($timestamp) ?></span>
</div>

<!-- KPI Cards -->
<div class="row g-4 mb-4" id="kpiCards">
    <!-- Total Besoins -->
    <div class="col-xl-3 col-md-6 animate__animated animate__fadeInUp">
        <div class="kpi-card kpi-primary">
            <div class="kpi-icon">
                <i class="bi bi-clipboard-data"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-value" id="besoinsTotal"><?= number_format($besoins_total, 0, ',', ' ') ?></div>
                <div class="kpi-label">Besoins Total (Ar)</div>
            </div>
            <div class="kpi-wave"></div>
        </div>
    </div>
    
    <!-- Total Satisfait -->
    <div class="col-xl-3 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
        <div class="kpi-card kpi-success">
            <div class="kpi-icon">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-value" id="satisfaitTotal"><?= number_format($satisfait_total, 0, ',', ' ') ?></div>
                <div class="kpi-label">Satisfait (Ar)</div>
            </div>
            <div class="kpi-wave"></div>
        </div>
    </div>
    
    <!-- Total Restant -->
    <div class="col-xl-3 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
        <div class="kpi-card kpi-warning">
            <div class="kpi-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-value" id="restantTotal"><?= number_format($restant_total, 0, ',', ' ') ?></div>
                <div class="kpi-label">Restant (Ar)</div>
            </div>
            <div class="kpi-wave"></div>
        </div>
    </div>
    
    <!-- Pourcentage -->
    <div class="col-xl-3 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
        <div class="kpi-card kpi-info">
            <div class="kpi-icon">
                <i class="bi bi-percent"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-value" id="pourcentageGlobal"><?= $pourcentage_global ?>%</div>
                <div class="kpi-label">Taux de Satisfaction</div>
            </div>
            <div class="kpi-wave"></div>
        </div>
    </div>
</div>

<!-- Progress Bar Global -->
<div class="content-card animate__animated animate__fadeInUp mb-4">
    <div class="content-card-header">
        <h3 class="content-card-title">
            <i class="bi bi-bar-chart me-2"></i>Progression Globale
        </h3>
    </div>
    <div class="content-card-body">
        <div class="progress progress-custom">
            <div class="progress-bar bg-success" id="progressDistrib" role="progressbar" 
                 style="width: <?= $besoins_total > 0 ? ($distributions_total / $besoins_total * 100) : 0 ?>%"
                 title="Distributions">
                <?php if ($besoins_total > 0 && ($distributions_total / $besoins_total * 100) > 10): ?>
                    <?= round($distributions_total / $besoins_total * 100) ?>%
                <?php endif; ?>
            </div>
            <div class="progress-bar bg-info" id="progressAchats" role="progressbar" 
                 style="width: <?= $besoins_total > 0 ? ($achats_total / $besoins_total * 100) : 0 ?>%"
                 title="Achats">
                <?php if ($besoins_total > 0 && ($achats_total / $besoins_total * 100) > 10): ?>
                    <?= round($achats_total / $besoins_total * 100) ?>%
                <?php endif; ?>
            </div>
        </div>
        
        <div class="legend-container">
            <div class="legend-item">
                <div class="legend-icon bg-success">
                    <i class="bi bi-gift-fill"></i>
                </div>
                <div class="legend-content">
                    <span class="legend-label">Distributions</span>
                    <span class="legend-value" id="distribMontant"><?= number_format($distributions_total, 0, ',', ' ') ?> Ar</span>
                </div>
            </div>
            
            <div class="legend-item">
                <div class="legend-icon bg-info">
                    <i class="bi bi-cart-fill"></i>
                </div>
                <div class="legend-content">
                    <span class="legend-label">Achats</span>
                    <span class="legend-value" id="achatsMontant"><?= number_format($achats_total, 0, ',', ' ') ?> Ar</span>
                </div>
            </div>
            
            <div class="legend-item">
                <div class="legend-icon bg-warning">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="legend-content">
                    <span class="legend-label">Restant</span>
                    <span class="legend-value" id="restantMontant"><?= number_format($restant_total, 0, ',', ' ') ?> Ar</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dons Argent Card -->
<div class="content-card animate__animated animate__fadeInUp mb-4">
    <div class="content-card-header bg-dark text-white">
        <h3 class="content-card-title">
            <i class="bi bi-cash-stack me-2"></i>Dons en Argent
        </h3>
    </div>
    <div class="content-card-body">
        <div class="row text-center">
            <div class="col-md-4">
                <h4 class="text-primary" id="donsArgentTotal"><?= number_format($dons_argent_total, 0, ',', ' ') ?> Ar</h4>
                <p class="text-muted">Total Reçu</p>
            </div>
            <div class="col-md-4">
                <h4 class="text-danger" id="donsArgentUtilises"><?= number_format($dons_argent_utilises, 0, ',', ' ') ?> Ar</h4>
                <p class="text-muted">Utilisé (Achats)</p>
            </div>
            <div class="col-md-4">
                <h4 class="text-success" id="donsArgentRestant"><?= number_format($dons_argent_restant, 0, ',', ' ') ?> Ar</h4>
                <p class="text-muted">Disponible</p>
            </div>
        </div>
    </div>
</div>

<!-- Tableau par Ville -->
<div class="content-card animate__animated animate__fadeInUp">
    <div class="content-card-header">
        <h3 class="content-card-title">
            <i class="bi bi-geo-alt me-2"></i>Détails par Ville
        </h3>
    </div>
    <div class="content-card-body">
        <div class="table-responsive">
            <table class="table table-custom" id="villeTable">
                <thead>
                    <tr>
                        <th>Ville</th>
                        <th class="text-end">Besoins (Ar)</th>
                        <th class="text-end">Distribué (Ar)</th>
                        <th class="text-end">Achats (Ar)</th>
                        <th class="text-end">Satisfait (Ar)</th>
                        <th class="text-end">Restant (Ar)</th>
                        <th class="text-center">Progression</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($par_ville as $ville): ?>
                        <tr data-ville-id="<?= $ville['id'] ?>">
                            <td><strong><?= htmlspecialchars($ville['ville_nom']) ?></strong></td>
                            <td class="text-end besoins-montant"><?= number_format($ville['besoins_montant'], 0, ',', ' ') ?></td>
                            <td class="text-end distribue-montant"><?= number_format($ville['distribue_montant'], 0, ',', ' ') ?></td>
                            <td class="text-end achats-montant"><?= number_format($ville['achats_montant'], 0, ',', ' ') ?></td>
                            <td class="text-end text-success satisfait-montant"><?= number_format($ville['satisfait_montant'], 0, ',', ' ') ?></td>
                            <td class="text-end text-warning restant-montant"><?= number_format($ville['restant_montant'], 0, ',', ' ') ?></td>
                            <td class="text-center" style="width: 180px;">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success ville-progress" role="progressbar" 
                                         style="width: <?= $ville['pourcentage'] ?>%">
                                        <?= $ville['pourcentage'] ?>%
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="/js/recap.js"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
