<?php
// app/views/pages/dispatch.php
include __DIR__ . '/../layouts/header.php';
?>

<!-- Page Header -->
<div class="page-header animate__animated animate__fadeIn">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">
                <i class="bi bi-arrow-left-right me-2"></i>Simulation de Dispatch
            </h1>
            <p class="page-subtitle">Attribuer automatiquement les dons disponibles aux besoins les plus anciens</p>
        </div>
    </div>
</div>

<!-- Flash Messages -->
<?php if (!empty($message)): ?>
    <div class="alert alert-success alert-dismissible fade show alert-custom animate__animated animate__fadeInDown" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show alert-custom animate__animated animate__fadeInDown" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Dispatch Action -->
<div class="row g-4 mb-4 animate__animated animate__fadeInUp">
    <div class="col-md-12">
        <div class="dispatch-hero">
            <div class="dispatch-hero-content">
                <div class="dispatch-icon">
                    <i class="bi bi-shuffle"></i>
                </div>
                <h3>Algorithme de Distribution</h3>
                <p class="text-muted mb-4">
                    L'algorithme va matcher chaque don avec les besoins correspondants (même type & description), 
                    en respectant l'ordre chronologique (FIFO).
                </p>
                <form action="/dispatch/run" method="POST" id="dispatchForm">
                    <button type="submit" class="btn btn-warning btn-lg btn-custom dispatch-btn" id="dispatchBtn">
                        <i class="bi bi-play-circle-fill me-2"></i>Lancer la Simulation
                    </button>
                </form>
            </div>
            <div class="dispatch-steps">
                <div class="dispatch-step">
                    <div class="step-number">1</div>
                    <div class="step-text">Récupérer besoins non satisfaits</div>
                </div>
                <div class="dispatch-step-arrow">
                    <i class="bi bi-arrow-right"></i>
                </div>
                <div class="dispatch-step">
                    <div class="step-number">2</div>
                    <div class="step-text">Matcher dons par type & desc.</div>
                </div>
                <div class="dispatch-step-arrow">
                    <i class="bi bi-arrow-right"></i>
                </div>
                <div class="dispatch-step">
                    <div class="step-number">3</div>
                    <div class="step-text">Créer les distributions</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Distributions Table -->
<div class="content-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
    <div class="content-card-header">
        <h3 class="content-card-title">
            <i class="bi bi-list-columns-reverse me-2"></i>Historique des Distributions
        </h3>
        <div class="content-card-actions">
            <span class="badge bg-primary-subtle text-primary">
                <?= count($distributions ?? []) ?> distribution(s)
            </span>
        </div>
    </div>
    <div class="content-card-body">
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th><i class="bi bi-geo-alt me-1"></i>Ville</th>
                        <th><i class="bi bi-clipboard me-1"></i>Besoin</th>
                        <th><i class="bi bi-gift me-1"></i>Don Utilisé</th>
                        <th><i class="bi bi-123 me-1"></i>Qté Attribuée</th>
                        <th><i class="bi bi-calendar me-1"></i>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($distributions)): ?>
                        <?php foreach ($distributions as $dist): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary-subtle text-primary rounded-circle me-2">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <?= htmlspecialchars($dist['ville_nom']) ?>
                                    </div>
                                </td>
                                <td><span class="badge bg-danger-subtle text-danger"><?= htmlspecialchars($dist['besoin_desc']) ?></span></td>
                                <td><span class="badge bg-success-subtle text-success"><?= htmlspecialchars($dist['don_desc']) ?></span></td>
                                <td><span class="badge bg-dark fs-6"><?= $dist['quantite_attribuee'] ?></span></td>
                                <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($dist['date_distribution'])) ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-arrow-left-right fs-1 text-muted"></i>
                                    <p class="text-muted mt-2">Aucune distribution effectuée</p>
                                    <small class="text-muted">Lancez la simulation pour générer des distributions</small>
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
