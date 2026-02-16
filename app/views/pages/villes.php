<?php
// app/views/pages/villes.php
include __DIR__ . '/../layouts/header.php';
?>

<!-- Page Header -->
<div class="page-header animate__animated animate__fadeIn">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">
                <i class="bi bi-geo-alt-fill me-2"></i>Gestion des Villes & Régions
            </h1>
            <p class="page-subtitle">Configurer les villes et régions des sinistrés</p>
        </div>
        <div class="col-auto">
            <span class="badge badge-count bg-primary-subtle text-primary">
                <i class="bi bi-building me-1"></i>
                <?= count($villes ?? []) ?> ville(s)
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
    <!-- Formulaires -->
    <div class="col-lg-4 animate__animated animate__fadeInLeft">
        <!-- Région -->
        <div class="content-card mb-4">
            <div class="content-card-header header-secondary">
                <h3 class="content-card-title">
                    <i class="bi bi-map me-2"></i>Nouvelle Région
                </h3>
            </div>
            <div class="content-card-body">
                <form action="/villes/region/store" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="region_nom" class="form-label fw-semibold">
                            <i class="bi bi-pin-map me-1 text-secondary"></i>Nom de la Région
                        </label>
                        <input type="text" class="form-control" id="region_nom" name="nom" placeholder="Ex: Atsinanana, Analamanga" required>
                        <div class="invalid-feedback">Le nom de la région est requis.</div>
                    </div>
                    <button type="submit" class="btn btn-secondary w-100 btn-custom">
                        <i class="bi bi-plus-lg me-2"></i>Ajouter Région
                    </button>
                </form>
            </div>
        </div>

        <!-- Ville -->
        <div class="content-card">
            <div class="content-card-header">
                <h3 class="content-card-title">
                    <i class="bi bi-building me-2"></i>Nouvelle Ville
                </h3>
            </div>
            <div class="content-card-body">
                <form action="/villes/store" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="ville_nom" class="form-label fw-semibold">
                            <i class="bi bi-geo-alt me-1 text-primary"></i>Nom de la Ville
                        </label>
                        <input type="text" class="form-control" id="ville_nom" name="nom" placeholder="Ex: Tamatave, Antananarivo" required>
                        <div class="invalid-feedback">Le nom de la ville est requis.</div>
                    </div>
                    <div class="mb-3">
                        <label for="region_id" class="form-label fw-semibold">
                            <i class="bi bi-map me-1 text-primary"></i>Région
                        </label>
                        <select class="form-select" id="region_id" name="region_id" required>
                            <option value="">Sélectionner une région</option>
                            <?php if (!empty($regions)): ?>
                                <?php foreach ($regions as $region): ?>
                                    <option value="<?= $region['id'] ?>"><?= htmlspecialchars($region['nom']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <div class="invalid-feedback">Veuillez sélectionner une région.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-custom">
                        <i class="bi bi-plus-lg me-2"></i>Ajouter Ville
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
                    <i class="bi bi-list-ul me-2"></i>Liste des Villes
                </h3>
                <div class="content-card-actions">
                    <input type="text" class="form-control form-control-sm search-input" id="searchVillesPage" placeholder="Rechercher...">
                </div>
            </div>
            <div class="content-card-body">
                <div class="table-responsive">
                    <table class="table table-custom" id="villesPageTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ville</th>
                                <th>Région</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($villes)): ?>
                                <?php foreach ($villes as $index => $ville): ?>
                                    <tr>
                                        <td><span class="text-muted"><?= $index + 1 ?></span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary-subtle text-primary rounded-circle me-2">
                                                    <i class="bi bi-building"></i>
                                                </div>
                                                <strong><?= htmlspecialchars($ville['nom']) ?></strong>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary">
                                                <i class="bi bi-map me-1"></i><?= htmlspecialchars($ville['region_nom'] ?? '') ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <form action="/villes/delete" method="POST" style="display:inline;" class="delete-form">
                                                <input type="hidden" name="id" value="<?= $ville['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-delete">
                                                    <i class="bi bi-trash3 me-1"></i>Supprimer
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="bi bi-geo-alt fs-1 text-muted"></i>
                                            <p class="text-muted mt-2">Aucune ville enregistrée</p>
                                            <small class="text-muted">Ajoutez d'abord une région, puis une ville</small>
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
