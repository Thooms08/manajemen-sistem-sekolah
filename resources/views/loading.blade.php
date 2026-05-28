<!-- ══════════════════ LOADING OVERLAY ══════════════════ -->
<div id="global-loading-overlay" aria-live="polite" aria-busy="false">
    <div class="glo-backdrop"></div>
    <div class="glo-card">
        <div class="glo-spinner"></div>
        <p class="glo-label" id="glo-label-text">Loading...</p>
    </div>
</div>

<style>
:root {
    --glo-primary: #16a34a; /* green-600 */
    --glo-surface: #ffffff;
    --glo-backdrop: rgba(255, 255, 255, 0.8);
}

#global-loading-overlay {
    position: fixed; inset: 0; z-index: 99999;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; visibility: hidden; transition: 0.3s;
}
#global-loading-overlay.glo-visible { opacity: 1; visibility: visible; }

.glo-backdrop {
    position: absolute; inset: 0;
    background: var(--glo-backdrop);
    backdrop-filter: blur(2px);
}

.glo-card {
    position: relative; z-index: 1;
    padding: 20px 40px;
    background: var(--glo-surface);
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    display: flex; flex-direction: column; align-items: center; gap: 15px;
}

/* Spinner Sederhana */
.glo-spinner {
    width: 30px; height: 30px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid var(--glo-primary);
    border-radius: 50%;
    animation: glo-spin 0.8s linear infinite;
}

@keyframes glo-spin {
    to { transform: rotate(360deg); }
}

.glo-label {
    font-family: sans-serif;
    font-size: 0.9rem;
    font-weight: 500;
    color: #374151;
    margin: 0;
}
</style>

<script>
(function () {
    const overlay = document.getElementById('global-loading-overlay');
    const labelEl = document.getElementById('glo-label-text');

    window.Loading = {
        show: (label) => {
            if (!overlay) return;
            labelEl.textContent = label || 'Loading...';
            overlay.classList.add('glo-visible');
        },
        hide: () => {
            if (!overlay) return;
            overlay.classList.remove('glo-visible');
        }
    };
})();

// Auto-intercept tetap berfungsi sama
document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('submit', (e) => {
        if (!e.target.hasAttribute('data-no-loading')) window.Loading.show();
    });
    
    document.addEventListener('click', (e) => {
        const a = e.target.closest('a[href]');
        if (a && !a.hasAttribute('data-no-loading') && !a.hasAttribute('data-bs-toggle') && a.getAttribute('href') !== '#') {
            window.Loading.show('Loading...');
        }
    });
    
    window.addEventListener('pageshow', () => window.Loading.hide());
});
</script>