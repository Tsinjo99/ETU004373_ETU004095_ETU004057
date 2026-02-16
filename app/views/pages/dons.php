<?php
// app/views/pages/dons.php
include __DIR__ . '/../layouts/header.php';
?>

<!-- Page Header -->
<div class="page-header animate__animated animate__fadeIn">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">
                <i class="bi bi-gift-fill me-2"></i>Gestion des Dons
            </h1>
            <p class="page-subtitle">Enregistrer les dons reçus pour les sinistrés</p>
        </div>
        <div class="col-auto">
            <span class="badge badge-count bg-success-subtle text-success">
                <i class="bi bi-gift me-1"></i>
                <?= count($dons ?? []) ?> don(s)
            </span>
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

<div class="row g-4">
    <!-- Formulaire -->
    <div class="col-lg-4 animate__animated animate__fadeInLeft">
        <div class="content-card sticky-form">
            <div class="content-card-header header-success">
                <h3 class="content-card-title">
                    <i class="bi bi-plus-circle me-2"></i>Nouveau Don
                </h3>
            </div>
            <div class="content-card-body">
                <form action="/dons/store" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="type_don" class="form-label fw-semibold">
                            <i class="bi bi-tag me-1 text-success"></i>Type de Don
                        </label>
                        <select class="form-select" id="type_don" name="type_don" required>
                            <option value="nature">🌾 Nature (Riz, Huile...)</option>
                            <option value="materiaux">🔨 Matériaux (Tôle, Clou...)</option>
                            <option value="argent">💰 Argent</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="don_description" class="form-label fw-semibold">
                            <i class="bi bi-card-text me-1 text-success"></i>Description
                        </label>
                        <input type="text" class="form-control" id="don_description" name="description" placeholder="Ex: Riz, Tôle, etc." required>
                        <div class="invalid-feedback">La description est requise.</div>
                    </div>
                    <div class="mb-3">
                        <label for="don_quantite" class="form-label fw-semibold">
                            <i class="bi bi-123 me-1 text-success"></i>Quantité
                        </label>
                        <input type="number" class="form-control" id="don_quantite" name="quantite" min="1" placeholder="0" required>
                        <div class="invalid-feedback">Quantité invalide.</div>
                    </div>
                    <button type="submit" class="btn btn-success w-100 btn-custom">
                        <i class="bi bi-heart-fill me-2"></i>Enregistrer le Don
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="col-lg-8 animate__animated animate__fadeInRight">
        <div class="content-card">
            <div class="content-card-header">
                <h3 class="content-card-title">
                    <i class="bi bi-clock-history me-2"></i>Historique des Dons
                </h3>
                <div class="content-card-actions">
                    <input type="text" class="form-control form-control-sm search-input" id="searchDons" placeholder="Rechercher...">
                </div>
            </div>
            <div class="content-card-body">
                <div class="table-responsive">
                    <table class="table table-custom" id="donsTable">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Quantité</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($dons)): ?>
                                <?php foreach ($dons as $don): ?>
                                    <tr>
                                        <td>
                                            <?php
                                            $type_icon = match($don['type_don']) {
                                                'nature' => '🌾',
                                                'materiaux' => '🔨',
                                                'argent' => '💰',
                                                default => '📦'
                                            };
                                            $type_class = match($don['type_don']) {
                                                'nature' => 'bg-success-subtle text-success',
                                                'materiaux' => 'bg-warning-subtle text-warning',
                                                'argent' => 'bg-info-subtle text-info',
                                                default => 'bg-secondary-subtle text-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $type_class ?>"><?= $type_icon ?> <?= htmlspecialchars($don['type_don']) ?></span>
                                        </td>
                                        <td><strong><?= htmlspecialchars($don['description']) ?></strong></td>
                                        <td><span class="badge bg-dark"><?= $don['quantite'] ?></span></td>
                                        <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($don['date_don'])) ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="bi bi-gift fs-1 text-muted"></i>
                                            <p class="text-muted mt-2">Aucun don enregistré</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include __DIR__ . '/../layouts/footer.php';
?>
