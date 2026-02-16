<?php
// app/views/pages/achats.php
include __DIR__ . '/../layouts/header.php';
?>

<!-- Page Header -->
<div class="page-header animate__animated animate__fadeIn" data-frais-achat="<?= $frais_achat ?>">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">
                <i class="bi bi-cart-check-fill me-2"></i>Achats via Dons Argent
            </h1>
            <p class="page-subtitle">Acheter des besoins nature/matériaux avec les dons en argent (frais: <?= $frais_achat ?>%)</p>
        </div>
        <div class="col-auto">
            <span class="badge badge-count bg-success-subtle text-success">
                <i class="bi bi-cart me-1"></i>
                <?= count($achats ?? []) ?> achat(s)
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
    <!-- Formulaire d'achat -->
    <div class="col-lg-4 animate__animated animate__fadeInLeft">
        <div class="content-card sticky-form">
            <div class="content-card-header header-success">
                <h3 class="content-card-title">
                    <i class="bi bi-cart-plus me-2"></i>Nouvel Achat
                </h3>
            </div>
            <div class="content-card-body">
                <?php if (empty($besoins_achetables)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>Aucun besoin nature/matériaux en attente.
                    </div>
                <?php elseif (empty($dons_argent)): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>Aucun don argent disponible.
                    </div>
                <?php else: ?>
                    <form action="/achats/store" method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="besoin_id" class="form-label fw-semibold">
                                <i class="bi bi-clipboard-check me-1 text-primary"></i>Besoin à acheter
                            </label>
                            <select class="form-select" id="besoin_id" name="besoin_id" required>
                                <option value="">Sélectionner un besoin</option>
                                <?php foreach ($besoins_achetables as $besoin): ?>
                                    <option value="<?= $besoin['id'] ?>" 
                                            data-prix="<?= $besoin['prix_unitaire'] ?>"
                                            data-max="<?= $besoin['quantite_restante'] ?>">
                                        <?= htmlspecialchars($besoin['ville_nom']) ?> - 
                                        <?= $besoin['type_besoin'] === 'nature' ? '🌾' : '🔨' ?>
                                        <?= htmlspecialchars($besoin['description']) ?>
                                        (reste: <?= $besoin['quantite_restante'] ?>, prix: <?= number_format($besoin['prix_unitaire'], 0) ?> Ar)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="don_id" class="form-label fw-semibold">
                                <i class="bi bi-cash-coin me-1 text-success"></i>Don Argent
                            </label>
                            <select class="form-select" id="don_id" name="don_id" required>
                                <option value="">Sélectionner un don argent</option>
                                <?php foreach ($dons_argent as $don): ?>
                                    <option value="<?= $don['id'] ?>" data-solde="<?= $don['solde_restant'] ?>">
                                        💰 <?= htmlspecialchars($don['description']) ?>
                                        (solde: <?= number_format($don['solde_restant'], 0) ?> Ar)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="quantite" class="form-label fw-semibold">
                                <i class="bi bi-123 me-1 text-primary"></i>Quantité
                            </label>
                            <input type="number" class="form-control" id="quantite" name="quantite" min="1" placeholder="0" required>
                            <div class="invalid-feedback">Quantité invalide.</div>
                        </div>
                        
                        <!-- Aperçu du calcul -->
                        <div class="alert alert-secondary mb-3" id="apercu-calcul" style="display: none;">
                            <small>
                                <strong>Aperçu:</strong><br>
                                Montant HT: <span id="montant-ht">0</span> Ar<br>
                                Frais (<?= $frais_achat ?>%): <span id="montant-frais">0</span> Ar<br>
                                <strong class="text-success">Total: <span id="montant-total">0</span> Ar</strong>
                            </small>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100 btn-custom">
                            <i class="bi bi-cart-check me-2"></i>Effectuer l'Achat
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Liste des achats -->
    <div class="col-lg-8 animate__animated animate__fadeInRight">
        <div class="content-card">
            <div class="content-card-header header-success">
                <h3 class="content-card-title">
                    <i class="bi bi-list-check me-2"></i>Liste des Achats
                </h3>
                <div class="content-card-actions">
                    <!-- Filtre par ville -->
                    <select class="form-select form-select-sm" id="ville-filter" style="width: auto; min-width: 150px;">
                        <option value="">Toutes les villes</option>
                        <?php foreach ($villes as $ville): ?>
                            <option value="<?= $ville['id'] ?>" <?= ($ville_id_filter == $ville['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ville['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="content-card-body p-0">
                <?php if (empty($achats)): ?>
                    <div class="text-center py-5 empty-state">
                        <i class="bi bi-cart-x display-1 text-muted"></i>
                        <p class="text-muted mt-3 mb-0">Aucun achat effectué</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ville</th>
                                    <th>Besoin</th>
                                    <th>Qté</th>
                                    <th>Montant HT</th>
                                    <th>Frais</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($achats as $index => $achat): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <span class="badge rounded-pill bg-secondary-subtle text-secondary">
                                                <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($achat['ville_nom']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= $achat['type_besoin'] === 'nature' ? '🌾' : '🔨' ?>
                                            <?= htmlspecialchars($achat['besoin_desc']) ?>
                                        </td>
                                        <td><strong><?= $achat['quantite_achetee'] ?></strong></td>
                                        <td><?= number_format($achat['montant_ht'], 0) ?> Ar</td>
                                        <td><span class="text-warning"><?= $achat['frais_pourcent'] ?>%</span></td>
                                        <td><strong class="text-success"><?= number_format($achat['montant_total'], 0) ?> Ar</strong></td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('d/m/Y H:i', strtotime($achat['date_achat'])) ?>
                                            </small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="/js/achats.js"></script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
