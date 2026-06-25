/**
 * Clinora – Global Vanilla JS
 * Handles: modals, flash messages, confirmations, pointers
 */

// ─── Modal System ───────────────────────────────────────────────
window.CModal = {
    open(id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('[data-modal-panel]')?.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
        });
    },
    close(id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.add('opacity-0');
        modal.querySelector('[data-modal-panel]')?.classList.add('scale-95', 'opacity-0', 'translate-y-4');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 200);
    },
    toggle(id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.contains('hidden') ? this.open(id) : this.close(id);
    }
};

// ─── Wire up data-modal-open / data-modal-close buttons ─────────
document.addEventListener('DOMContentLoaded', () => {
    // Open buttons
    document.querySelectorAll('[data-modal-open]').forEach(btn => {
        btn.addEventListener('click', () => CModal.open(btn.dataset.modalOpen));
    });

    // Close buttons
    document.querySelectorAll('[data-modal-close]').forEach(btn => {
        btn.addEventListener('click', () => CModal.close(btn.dataset.modalClose));
    });

    // Click outside backdrop to close
    document.querySelectorAll('[data-modal]').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) CModal.close(modal.id);
        });
    });

    // ESC key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('[data-modal]:not(.hidden)').forEach(m => CModal.close(m.id));
        }
    });

    // ─── Auto-dismiss flash messages ────────────────────────────
    document.querySelectorAll('[data-flash]').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        }, 4000);
    });

    // ─── Confirmation dialogs ────────────────────────────────────
    document.querySelectorAll('[data-confirm]').forEach(form => {
        form.addEventListener('submit', (e) => {
            const msg = form.dataset.confirm;
            if (!confirm(msg)) e.preventDefault();
        });
    });
});

