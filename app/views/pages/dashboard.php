<?php
// app/views/pages/dashboard.php
include __DIR__ . '/../layouts/header.php';
?>

<!-- Page Header -->
<div class="page-header animate__animated animate__fadeIn">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">
                <i class="bi bi-speedometer2 me-2"></i>Tableau de Bord
            </h1>
            <p class="page-subtitle">Suivi en temps réel des besoins et distributions pour les sinistrés</p>
        </div>
        <div class="col-auto">
            <span class="badge badge-date">
                <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y') ?>
            </span>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-4 mb-5 animate__animated animate__fadeInUp">
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card kpi-primary">
            <div class="kpi-icon">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-value" data-count="<?= $stats['total_villes'] ?? 0 ?>"><?= $stats['total_villes'] ?? 0 ?></div>
                <div class="kpi-label">Villes Enregistrées</div>
            </div>
            <div class="kpi-wave"></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card kpi-danger">
            <div class="kpi-icon">
                <i class="bi bi-clipboard2-pulse-fill"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-value" data-count="<?= $stats['total_besoins'] ?? 0 ?>"><?= $stats['total_besoins'] ?? 0 ?></div>
                <div class="kpi-label">Besoins Totaux</div>
            </div>
            <div class="kpi-wave"></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card kpi-success">
            <div class="kpi-icon">
                <i class="bi bi-gift-fill"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-value" data-count="<?= $stats['total_dons'] ?? 0 ?>"><?= $stats['total_dons'] ?? 0 ?></div>
                <div class="kpi-label">Dons Reçus</div>
            </div>
            <div class="kpi-wave"></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="kpi-card kpi-info">
            <div class="kpi-icon">
                <i class="bi bi-pie-chart-fill"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-value"><?= $stats['taux_satisfaction'] ?? '0%' ?></div>
                <div class="kpi-label">Taux de Satisfaction</div>
            </div>
            <div class="kpi-wave"></div>
        </div>
    </div>
</div>

<!-- Villes Table -->
<div class="content-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
    <div class="content-card-header">
        <h3 class="content-card-title">
            <i class="bi bi-map me-2"></i>État des Villes
        </h3>
        <div class="content-card-actions">
            <input type="text" class="form-control form-control-sm search-input" id="searchVilles" placeholder="Rechercher une ville...">
        </div>
    </div>
    <div class="content-card-body">
        <div class="table-responsive">
            <table class="table table-custom" id="villesTable">
                <thead>
                    <tr>
                        <th><i class="bi bi-geo-alt me-1"></i>Ville</th>
                        <th><i class="bi bi-map me-1"></i>Région</th>
                        <th><i class="bi bi-list-check me-1"></i>Besoins (Détails)</th>
                        <th><i class="bi bi-gift me-1"></i>Dons Attribués</th>
                        <th><i class="bi bi-flag me-1"></i>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($villes_stats)): ?>
                        <?php foreach ($villes_stats as $ville): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary-subtle text-primary rounded-circle me-2">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <strong><?= htmlspecialchars($ville['nom']) ?></strong>
                                    </div>
                                </td>
                                <td><span class="badge bg-secondary-subtle text-secondary"><?= htmlspecialchars($ville['region']) ?></span></td>
                                <td><small><?= htmlspecialchars($ville['besoins_list'] ?? 'Aucun besoin') ?></small></td>
                                <td><span class="fw-bold"><?= $ville['total_dons_recus'] ?></span></td>
                                <td>
                                    <?php
                                    $badge_class = match($ville['status_class']) {
                                        'status-satisfait' => 'status-badge status-satisfait',
                                        'status-partiel' => 'status-badge status-partiel',
                                        'status-non' => 'status-badge status-non',
                                        default => 'status-badge status-none'
                                    };
                                    ?>
                                    <span class="<?= $badge_class ?>">
                                        <?= $ville['status_text'] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                    <p class="text-muted mt-2">Aucune donnée disponible</p>
                                    <a href="/villes" class="btn btn-sm btn-outline-primary">Ajouter une ville</a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
include __DIR__ . '/../layouts/footer.php';
?>
