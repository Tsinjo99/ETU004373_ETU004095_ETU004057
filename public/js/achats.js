// Achats page - Filtre par ville et calcul dynamique
document.addEventListener('DOMContentLoaded', function() {
    // Filtre par ville
    const villeFilterSelect = document.getElementById('ville-filter');
    if (villeFilterSelect) {
        villeFilterSelect.addEventListener('change', function() {
            const villeId = this.value;
            if (villeId) {
                window.location.href = '/achats?ville_id=' + villeId;
            } else {
                window.location.href = '/achats';
            }
        });
    }
    
    // Calcul dynamique du montant
    setupCalculCallbacks();
});

function setupCalculCallbacks() {
    const besoinSelect = document.getElementById('besoin_id');
    const quantiteInput = document.getElementById('quantite');
    const apercuDiv = document.getElementById('apercu-calcul');
    
    if (!besoinSelect || !quantiteInput) return;
    
    // Récupérer le pourcentage de frais du data attribute du conteneur
    const fraisPercent = document.querySelector('[data-frais-achat]')?.dataset.fraisAchat || 10;
    
    function updateApercu() {
        const selectedOption = besoinSelect.options[besoinSelect.selectedIndex];
        const prix = parseFloat(selectedOption?.dataset?.prix || 0);
        const quantite = parseInt(quantiteInput.value || 0);
        
        if (prix > 0 && quantite > 0) {
            const montantHt = prix * quantite;
            const montantFrais = montantHt * (fraisPercent / 100);
            const montantTotal = montantHt + montantFrais;
            
            document.getElementById('montant-ht').textContent = montantHt.toLocaleString('fr-FR');
            document.getElementById('montant-frais').textContent = montantFrais.toLocaleString('fr-FR');
            document.getElementById('montant-total').textContent = montantTotal.toLocaleString('fr-FR');
            
            if (apercuDiv) apercuDiv.style.display = 'block';
        } else {
            if (apercuDiv) apercuDiv.style.display = 'none';
        }
    }
    
    besoinSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const maxQte = parseInt(selectedOption?.dataset?.max || 1);
        quantiteInput.max = maxQte;
        quantiteInput.placeholder = `Max: ${maxQte}`;
        updateApercu();
    });
    
    quantiteInput.addEventListener('input', updateApercu);
}
