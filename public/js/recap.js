// Récap page - Actualisation et mise à jour des KPI
document.addEventListener('DOMContentLoaded', function() {
    const refreshBtn = document.getElementById('refreshBtn');
    
    if (!refreshBtn) return;
    
    refreshBtn.addEventListener('click', function() {
        refreshBtn.disabled = true;
        refreshBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Chargement...';
        
        fetch('/recap/data')
            .then(response => response.json())
            .then(data => {
                updateKPIs(data);
                updateProgressBars(data);
                updateMoneySection(data);
                updateVilleTable(data);
                updateTimestamp(data);
                animateCards();
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
    
    function updateKPIs(data) {
        document.getElementById('besoinsTotal').textContent = formatNumber(data.besoins_total);
        document.getElementById('satisfaitTotal').textContent = formatNumber(data.satisfait_total);
        document.getElementById('restantTotal').textContent = formatNumber(data.restant_total);
        document.getElementById('pourcentageGlobal').textContent = data.pourcentage_global + '%';
    }
    
    function updateProgressBars(data) {
        const distribPct = data.besoins_total > 0 ? (data.distributions_total / data.besoins_total * 100) : 0;
        const achatsPct = data.besoins_total > 0 ? (data.achats_total / data.besoins_total * 100) : 0;
        
        document.getElementById('progressDistrib').style.width = distribPct + '%';
        document.getElementById('progressAchats').style.width = achatsPct + '%';
        
        document.getElementById('distribMontant').textContent = formatNumber(data.distributions_total) + ' Ar';
        document.getElementById('achatsMontant').textContent = formatNumber(data.achats_total) + ' Ar';
        document.getElementById('restantMontant').textContent = formatNumber(data.restant_total) + ' Ar';
    }
    
    function updateMoneySection(data) {
        document.getElementById('donsArgentTotal').textContent = formatNumber(data.dons_argent_total) + ' Ar';
        document.getElementById('donsArgentUtilises').textContent = formatNumber(data.dons_argent_utilises) + ' Ar';
        document.getElementById('donsArgentRestant').textContent = formatNumber(data.dons_argent_restant) + ' Ar';
    }
    
    function updateVilleTable(data) {
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
    }
    
    function updateTimestamp(data) {
        document.getElementById('lastUpdate').textContent = data.timestamp;
    }
    
    function animateCards() {
        document.querySelectorAll('.kpi-card').forEach(card => {
            card.classList.add('animate__animated', 'animate__pulse');
            setTimeout(() => {
                card.classList.remove('animate__animated', 'animate__pulse');
            }, 500);
        });
    }
    
    function formatNumber(num) {
        return new Intl.NumberFormat('fr-FR').format(num);
    }
});
