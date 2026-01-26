/**
 * Custom Alert & Confirmation System for Obertrack
 * Modern toast notifications and modals to replace native alert() and confirm()
 */

// --- TOASTS ---

window.showAlert = function (message, type = 'info') {
    const container = document.getElementById('alert-container') || createAlertContainer();

    const alertId = 'alert-' + Date.now();
    const alert = document.createElement('div');
    alert.id = alertId;
    alert.className = 'alert-toast transform transition-all duration-300 ease-in-out translate-x-full opacity-0';

    const icons = {
        success: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>`,
        error: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>`,
        warning: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>`,
        info: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>`
    };

    const colors = {
        success: 'bg-green-50 border-green-200 text-green-800',
        error: 'bg-red-50 border-red-200 text-red-800',
        warning: 'bg-yellow-50 border-yellow-200 text-yellow-800',
        info: 'bg-blue-50 border-blue-200 text-blue-800'
    };

    const iconColors = {
        success: 'text-green-500',
        error: 'text-red-500',
        warning: 'text-yellow-500',
        info: 'text-blue-500'
    };

    alert.innerHTML = `
        <div class="flex items-start gap-3 ${colors[type]} border rounded-xl px-4 py-3 shadow-lg max-w-md">
            <div class="${iconColors[type]} flex-shrink-0 mt-0.5">
                ${icons[type]}
            </div>
            <div class="flex-1 text-sm font-medium leading-relaxed">
                ${escapeHtml(message)}
            </div>
            <button onclick="closeAlert('${alertId}')" class="flex-shrink-0 ml-2 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    `;

    container.appendChild(alert);

    setTimeout(() => {
        alert.classList.remove('translate-x-full', 'opacity-0');
    }, 10);

    setTimeout(() => {
        closeAlert(alertId);
    }, 5000);
};

window.closeAlert = function (alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => alert.remove(), 300);
    }
};

function createAlertContainer() {
    const container = document.createElement('div');
    container.id = 'alert-container';
    container.className = 'fixed top-4 right-4 z-[10000] flex flex-col gap-2';
    container.style.maxWidth = '90vw';
    document.body.appendChild(container);
    return container;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// --- CONFIRMATION MODAL ---

window.showConfirm = function (title, message, confirmText = 'Confirmar', cancelText = 'Cancelar', type = 'warning') {
    return new Promise((resolve) => {
        // Create modal overlay
        const overlay = document.createElement('div');
        overlay.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[10001] flex items-center justify-center fade-in';
        overlay.id = 'confirm-modal-overlay';

        let iconHtml = '';
        if (type === 'warning') {
            iconHtml = `<div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>`;
        } else if (type === 'info') {
            iconHtml = `<div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                 <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 100-18 9 9 0 000 18z" />
                 </svg>
             </div>`;
        }

        const modalHtml = `
            <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-2xl bg-white scale-in">
                <div class="mt-3 text-center">
                    ${iconHtml}
                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-2">${escapeHtml(title)}</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            ${escapeHtml(message)}
                        </p>
                    </div>
                    <div class="items-center px-4 py-3 mt-4 flex justify-center gap-4">
                        <button id="confirm-cancel-btn" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-lg w-full shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            ${escapeHtml(cancelText)}
                        </button>
                        <button id="confirm-ok-btn" class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-lg w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            ${escapeHtml(confirmText)}
                        </button>
                    </div>
                </div>
            </div>
        `;

        overlay.innerHTML = modalHtml;
        document.body.appendChild(overlay);

        const close = (result) => {
            overlay.classList.add('opacity-0');
            setTimeout(() => overlay.remove(), 200);
            resolve(result);
        };

        document.getElementById('confirm-ok-btn').onclick = () => close(true);
        document.getElementById('confirm-cancel-btn').onclick = () => close(false);
        overlay.onclick = (e) => {
            if (e.target === overlay) close(false);
        }
    });
};

// Global helper for form submissions
window.confirmFormSubmit = function (event, message) {
    event.preventDefault();
    const form = event.target;

    window.showConfirm('Confirmar Acción', message, 'Confirmar', 'Cancelar')
        .then(isConfirmed => {
            if (isConfirmed) {
                // If it's a form with Alpine/Livewire hijacking, regular submit might fail.
                // But for standard forms:
                form.submit();
                // If that doesn't work (e.g. handled by other JS), we assume user logic handles it?
                // No, standard HTML forms use submit().
            }
        });
    return false;
};

// Convenience methods
window.showSuccess = (message) => showAlert(message, 'success');
window.showError = (message) => showAlert(message, 'error');
window.showWarning = (message) => showAlert(message, 'warning');
window.showInfo = (message) => showAlert(message, 'info');

// Add styles dynamically (just in case tailwind classes are purged or not enough)
const style = document.createElement('style');
style.textContent = `
    .alert-toast { transform: translateX(0); opacity: 1; }
    .fade-in { animation: modalFadeIn 0.2s ease-out forwards; }
    .scale-in { animation: modalScaleIn 0.2s ease-out forwards; }
    @keyframes modalFadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes modalScaleIn { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
`;
document.head.appendChild(style);
