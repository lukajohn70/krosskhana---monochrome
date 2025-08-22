// Toast Notification System for Admin Panel
class ToastNotifications {
    constructor() {
        this.container = null;
        this.init();
    }

    init() {
        // Create toast container if it doesn't exist
        if (!document.getElementById('toast-container')) {
            this.container = document.createElement('div');
            this.container.id = 'toast-container';
            this.container.className = 'toast-container';
            document.body.appendChild(this.container);
        } else {
            this.container = document.getElementById('toast-container');
        }
    }

    show(message, type = 'success', duration = 5000) {
        if (!this.container) return;

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        
        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };

        const colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };

        const titles = {
            success: 'Success',
            error: 'Error',
            warning: 'Warning',
            info: 'Information'
        };

        toast.innerHTML = `
            <i class="toast-icon ${icons[type]}" style="color: ${colors[type]}"></i>
            <div class="toast-content">
                <div class="toast-title">${titles[type]}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;

        this.container.appendChild(toast);

        // Trigger animation
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);

        // Auto remove after specified duration
        setTimeout(() => {
            if (toast.parentElement) {
                toast.classList.remove('show');
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 300);
            }
        }, duration);
    }

    success(message, duration = 5000) {
        this.show(message, 'success', duration);
    }

    error(message, duration = 5000) {
        this.show(message, 'error', duration);
    }

    warning(message, duration = 5000) {
        this.show(message, 'warning', duration);
    }

    info(message, duration = 5000) {
        this.show(message, 'info', duration);
    }
}

// Initialize toast notifications after DOM is loaded
let toast = null;

function initializeToast() {
    if (!toast) {
        toast = new ToastNotifications();
    }
}

// Global functions for backward compatibility
function showSuccess(message) {
    if (!toast) initializeToast();
    if (toast) toast.success(message);
}

function showError(message) {
    if (!toast) initializeToast();
    if (toast) toast.error(message);
}

function showWarning(message) {
    if (!toast) initializeToast();
    if (toast) toast.warning(message);
}

function showInfo(message) {
    if (!toast) initializeToast();
    if (toast) toast.info(message);
}

function showToast(message, type = 'success') {
    if (!toast) initializeToast();
    if (toast) toast.show(message, type);
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeToast);
} else {
    initializeToast();
}
