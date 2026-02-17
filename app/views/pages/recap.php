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
        <div class="stat-card bg-primary">
            <div class="stat-icon">
                <i class="bi bi-clipboard-data"></i>
            </div>
            <div class="stat-content">
                <h3 id="besoinsTotal"><?= number_format($besoins_total, 0, ',', ' ') ?></h3>
                <p>Besoins Total (Ar)</p>
            </div>
        </div>
    </div>
    
    <!-- Total Satisfait -->
    <div class="col-xl-3 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
        <div class="stat-card bg-success">
            <div class="stat-icon">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3 id="satisfaitTotal"><?= number_format($satisfait_total, 0, ',', ' ') ?></h3>
                <p>Satisfait (Ar)</p>
            </div>
        </div>
    </div>
    
    <!-- Total Restant -->
    <div class="col-xl-3 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
        <div class="stat-card bg-warning">
            <div class="stat-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <h3 id="restantTotal"><?= number_format($restant_total, 0, ',', ' ') ?></h3>
                <p>Restant (Ar)</p>
            </div>
        </div>
    </div>
    
    <!-- Pourcentage -->
    <div class="col-xl-3 col-md-6 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
        <div class="stat-card bg-info">
            <div class="stat-icon">
                <i class="bi bi-percent"></i>
            </div>
            <div class="stat-content">
                <h3 id="pourcentageGlobal"><?= $pourcentage_global ?>%</h3>
                <p>Taux de Satisfaction</p>
            </div>
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
        <div class="progress" style="height: 30px;">
            <div class="progress-bar bg-success" id="progressDistrib" role="progressbar" 
                 style="width: <?= $besoins_total > 0 ? ($distributions_total / $besoins_total * 100) : 0 ?>%"
                 title="Distributions">
                <?php if ($besoins_total > 0 && ($distributions_total / $besoins_total * 100) > 10): ?>
                    Distributions
                <?php endif; ?>
            </div>
            <div class="progress-bar bg-info" id="progressAchats" role="progressbar" 
                 style="width: <?= $besoins_total > 0 ? ($achats_total / $besoins_total * 100) : 0 ?>%"
                 title="Achats">
                <?php if ($besoins_total > 0 && ($achats_total / $besoins_total * 100) > 10): ?>
                    Achats
                <?php endif; ?>
            </div>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <div>
                <span class="badge bg-success me-2">■</span>Distributions: 
                <strong id="distribMontant"><?= number_format($distributions_total, 0, ',', ' ') ?> Ar</strong>
            </div>
            <div>
                <span class="badge bg-info me-2">■</span>Achats: 
                <strong id="achatsMontant"><?= number_format($achats_total, 0, ',', ' ') ?> Ar</strong>
            </div>
            <div>
                <span class="badge bg-warning me-2">■</span>Restant: 
                <strong id="restantMontant"><?= number_format($restant_total, 0, ',', ' ') ?> Ar</strong>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const refreshBtn = document.getElementById('refreshBtn');
    
    refreshBtn.addEventListener('click', function() {
        refreshBtn.disabled = true;
        refreshBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Chargement...';
        
        fetch('/recap/data')
            .then(response => response.json())
            .then(data => {
                // Update KPIs
                document.getElementById('besoinsTotal').textContent = formatNumber(data.besoins_total);
                document.getElementById('satisfaitTotal').textContent = formatNumber(data.satisfait_total);
                document.getElementById('restantTotal').textContent = formatNumber(data.restant_total);
                document.getElementById('pourcentageGlobal').textContent = data.pourcentage_global + '%';
                
                // Update progress bar
                const distribPct = data.besoins_total > 0 ? (data.distributions_total / data.besoins_total * 100) : 0;
                const achatsPct = data.besoins_total > 0 ? (data.achats_total / data.besoins_total * 100) : 0;
                document.getElementById('progressDistrib').style.width = distribPct + '%';
                document.getElementById('progressAchats').style.width = achatsPct + '%';
                
                document.getElementById('distribMontant').textContent = formatNumber(data.distributions_total) + ' Ar';
                document.getElementById('achatsMontant').textContent = formatNumber(data.achats_total) + ' Ar';
                document.getElementById('restantMontant').textContent = formatNumber(data.restant_total) + ' Ar';
                
                // Update dons argent
                document.getElementById('donsArgentTotal').textContent = formatNumber(data.dons_argent_total) + ' Ar';
                document.getElementById('donsArgentUtilises').textContent = formatNumber(data.dons_argent_utilises) + ' Ar';
                document.getElementById('donsArgentRestant').textContent = formatNumber(data.dons_argent_restant) + ' Ar';
                
                // Update ville table
                data.par_ville.forEach(ville => {
                    const row = document.querySelector(`tr[data-ville-id="${ville.id}"]`);
                    if (row) {
                        row.querySelector('.besoins-montant').textContent = formatNumber(ville.besoins_montant);
                        row.querySelector('.distribue-montant').textContent = formatNumber(ville.distribue_montant);
                        row.querySelector('.achats-montant').textContent = formatNumber(ville.achats_montant);
                        row.querySelector('.satisfait-montant').textContent = formatNumber(ville.satisfait_montant);
                        row.querySelector('.restant-montant').textContent = formatNumber(ville.restant_montant);
                        const progressBar = row.querySelector('.ville-progress');
                        progressBar.style.width = ville.pourcentage + '%';
                        progressBar.textContent = ville.pourcentage + '%';
                    }
                });
                
                // Update timestamp
                document.getElementById('lastUpdate').textContent = data.timestamp;
                
                // Flash animation
                document.querySelectorAll('.stat-card').forEach(card => {
                    card.classList.add('animate__animated', 'animate__pulse');
                    setTimeout(() => {
                        card.classList.remove('animate__animated', 'animate__pulse');
                    }, 500);
                });
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'actualisation');
            })
            .finally(() => {
                refreshBtn.disabled = false;
                refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Actualiser';
            });
    });
    
    function formatNumber(num) {
        return new Intl.NumberFormat('fr-FR').format(num);
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
