<?php
// app/views/pages/besoins.php
include __DIR__ . '/../layouts/header.php';
?>

<!-- Page Header -->
<div class="page-header animate__animated animate__fadeIn">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">
                <i class="bi bi-clipboard2-pulse-fill me-2"></i>Gestion des Besoins
            </h1>
            <p class="page-subtitle">Enregistrer et suivre les besoins des sinistrés par ville</p>
        </div>
        <div class="col-auto">
            <span class="badge badge-count bg-danger-subtle text-danger">
                <i class="bi bi-clipboard-check me-1"></i>
                <?= count($besoins ?? []) ?> besoin(s)
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
            <div class="content-card-header">
                <h3 class="content-card-title">
                    <i class="bi bi-plus-circle me-2"></i>Nouveau Besoin
                </h3>
            </div>
            <div class="content-card-body">
                <form action="/besoins/store" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="ville_id" class="form-label fw-semibold">
                            <i class="bi bi-geo-alt me-1 text-primary"></i>Ville
                        </label>
                        <select class="form-select" id="ville_id" name="ville_id" required>
                            <option value="">Sélectionner une ville</option>
                            <?php if (!empty($villes)): ?>
                                <?php foreach ($villes as $ville): ?>
                                    <option value="<?= $ville['id'] ?>"><?= htmlspecialchars($ville['nom']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <div class="invalid-feedback">Veuillez sélectionner une ville.</div>
                    </div>
                    <div class="mb-3">
                        <label for="type_besoin" class="form-label fw-semibold">
                            <i class="bi bi-tag me-1 text-primary"></i>Type de Besoin
                        </label>
                        <select class="form-select" id="type_besoin" name="type_besoin" required>
                            <option value="nature">🌾 Nature (Riz, Huile...)</option>
                            <option value="materiaux">🔨 Matériaux (Tôle, Clou...)</option>
                            <option value="argent">💰 Argent</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">
                            <i class="bi bi-card-text me-1 text-primary"></i>Description
                        </label>
                        <input type="text" class="form-control" id="description" name="description" placeholder="Ex: Riz, Tôle, etc." required>
                        <div class="invalid-feedback">La description est requise.</div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="quantite" class="form-label fw-semibold">
                                <i class="bi bi-123 me-1 text-primary"></i>Quantité
                            </label>
                            <input type="number" class="form-control" id="quantite" name="quantite" min="1" placeholder="0" required>
                            <div class="invalid-feedback">Quantité invalide.</div>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="prix_unitaire" class="form-label fw-semibold">
                                <i class="bi bi-currency-exchange me-1 text-primary"></i>Prix Unit.
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="prix_unitaire" name="prix_unitaire" step="0.01" min="0" placeholder="0.00" required>
                                <span class="input-group-text">Ar</span>
                            </div>
                            <div class="invalid-feedback">Prix invalide.</div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-custom">
                        <i class="bi bi-save me-2"></i>Enregistrer le Besoin
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
                    <i class="bi bi-table me-2"></i>Liste des Besoins
                </h3>
                <div class="content-card-actions">
                    <input type="text" class="form-control form-control-sm search-input" id="searchBesoins" placeholder="Rechercher...">
                </div>
            </div>
            <div class="content-card-body">
                <div class="table-responsive">
                    <table class="table table-custom" id="besoinsTable">
                        <thead>
                            <tr>
                                <th>Ville</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Quantité</th>
                                <th>Prix Unit.</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($besoins)): ?>
                                <?php foreach ($besoins as $besoin): ?>
                                    <tr class="animate__animated animate__fadeIn">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary-subtle text-primary rounded-circle me-2">
                                                    <i class="bi bi-building"></i>
                                                </div>
                                                <?= htmlspecialchars($besoin['ville_nom']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $type_icon = match($besoin['type_besoin']) {
                                                'nature' => '🌾',
                                                'materiaux' => '🔨',
                                                'argent' => '💰',
                                                default => '📦'
                                            };
                                            $type_class = match($besoin['type_besoin']) {
                                                'nature' => 'bg-success-subtle text-success',
                                                'materiaux' => 'bg-warning-subtle text-warning',
                                                'argent' => 'bg-info-subtle text-info',
                                                default => 'bg-secondary-subtle text-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $type_class ?>"><?= $type_icon ?> <?= htmlspecialchars($besoin['type_besoin']) ?></span>
                                        </td>
                                        <td><strong><?= htmlspecialchars($besoin['description']) ?></strong></td>
                                        <td><span class="badge bg-dark"><?= $besoin['quantite'] ?></span></td>
                                        <td><?= number_format($besoin['prix_unitaire'], 2, ',', ' ') ?> Ar</td>
                                        <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($besoin['date_besoin'])) ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="bi bi-clipboard-x fs-1 text-muted"></i>
                                            <p class="text-muted mt-2">Aucun besoin enregistré</p>
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
