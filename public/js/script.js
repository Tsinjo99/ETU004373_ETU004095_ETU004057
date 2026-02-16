/**
 * BNGRC - Gestion des Dons
 * Main Application JavaScript
 */
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    /* ============================================
       LIVE CLOCK
       ============================================ */
    const liveClock = document.getElementById('live-clock');
    if (liveClock) {
        function updateClock() {
            const now = new Date();
            liveClock.textContent = now.toLocaleTimeString('fr-FR', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }
        updateClock();
        setInterval(updateClock, 1000);
    }

    /* ============================================
       NAVBAR SCROLL EFFECT
       ============================================ */
    const navbar = document.querySelector('.navbar-main');
    if (navbar) {
        window.addEventListener('scroll', function () {
            navbar.classList.toggle('scrolled', window.scrollY > 10);
        });
    }

    /* ============================================
       KPI COUNT-UP ANIMATION
       ============================================ */
    document.querySelectorAll('[data-count]').forEach(function (el) {
        const target = parseInt(el.getAttribute('data-count'), 10);
        if (isNaN(target) || target === 0) {
            el.textContent = '0';
            return;
        }
        const duration = 1200;
        const steps = 40;
        const stepTime = duration / steps;
        let current = 0;
        const increment = target / steps;

        const counter = setInterval(function () {
            current += increment;
            if (current >= target) {
                el.textContent = target.toLocaleString('fr-FR');
                clearInterval(counter);
            } else {
                el.textContent = Math.floor(current).toLocaleString('fr-FR');
            }
        }, stepTime);
    });

    /* ============================================
       SEARCH / FILTER TABLES
       ============================================ */
    const searchInputs = document.querySelectorAll(
        '#searchVilles, #searchBesoins, #searchDons, #searchVillesPage'
    );

    searchInputs.forEach(function (input) {
        input.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            const card = this.closest('.content-card') || this.closest('.card');
            if (!card) return;
            const table = card.querySelector('.table-custom') || card.querySelector('table');
            if (!table) return;
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(function (row) {
                if (row.querySelector('.empty-state')) return;
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    });

    /* ============================================
       BOOTSTRAP FORM VALIDATION
       ============================================ */
    document.querySelectorAll('.needs-validation').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    /* ============================================
       DELETE CONFIRMATION
       ============================================ */
    document.querySelectorAll('.delete-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.')) {
                form.submit();
            }
        });
    });

    /* ============================================
       DISPATCH BUTTON LOADING STATE
       ============================================ */
    const dispatchForm = document.getElementById('dispatchForm');
    const dispatchBtn = document.getElementById('dispatchBtn');
    if (dispatchForm && dispatchBtn) {
        dispatchForm.addEventListener('submit', function () {
            dispatchBtn.classList.add('loading');
            dispatchBtn.innerHTML =
                '<span class="spinner-dispatch"></span> Dispatch en cours...';
            dispatchBtn.disabled = true;
        });
    }

    /* ============================================
       AUTO-DISMISS ALERTS
       ============================================ */
    document.querySelectorAll('.alert-custom').forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) {
                alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(function () {
                    bsAlert.close();
                }, 500);
            }
        }, 5000);
    });

    /* ============================================
       TOOLTIPS
       ============================================ */
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el);
    });

    /* ============================================
       TABLE ROW INDEX (SEQUENTIAL NUMBERING)
       ============================================ */
    document.querySelectorAll('.table-custom').forEach(function (table) {
        const rows = table.querySelectorAll('tbody tr:not(.empty-state-row)');
        rows.forEach(function (row, index) {
            const numCell = row.querySelector('.row-number');
            if (numCell) {
                numCell.textContent = index + 1;
            }
        });
    });

    console.log('BNGRC App Loaded');
});
