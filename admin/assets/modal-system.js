// Modal System for Admin Panel
class ModalSystem {
    constructor() {
        this.activeModals = new Set();
    }

    // Create and show a modal
    show(options) {
        const {
            id = 'modal-' + Date.now(),
            title = '',
            content = '',
            type = 'info', // info, success, error, warning
            buttons = [],
            onClose = null,
            onConfirm = null,
            showCloseButton = true,
            closeOnOverlayClick = true
        } = options;

        // Remove existing modal with same ID
        this.hide(id);

        const modal = document.createElement('div');
        modal.id = id;
        modal.className = 'modal-overlay';
        
        // Create button HTML
        let buttonHTML = '';
        if (buttons.length > 0) {
            buttonHTML = '<div class="modal-footer">';
            buttons.forEach(button => {
                const btnClass = this.getButtonClass(button.type || 'secondary');
                buttonHTML += `<button class="modal-btn ${btnClass}" onclick="${button.onClick || ''}">${button.text}</button>`;
            });
            buttonHTML += '</div>';
        }

        modal.innerHTML = `
            <div class="modal ${type}">
                <div class="modal-header">
                    <h3 class="modal-title">${title}</h3>
                    ${showCloseButton ? `<button class="modal-close" onclick="modalSystem.hide('${id}')">
                        <i class="fas fa-times"></i>
                    </button>` : ''}
                </div>
                <div class="modal-body">
                    ${content}
                </div>
                ${buttonHTML}
            </div>
        `;

        // Add event listeners
        if (closeOnOverlayClick) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.hide(id);
                }
            });
        }

        // Store modal reference
        this.activeModals.add(id);
        modal.dataset.onClose = onClose;
        modal.dataset.onConfirm = onConfirm;

        document.body.appendChild(modal);

        // Trigger animation
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);

        return modal;
    }

    // Hide a specific modal
    hide(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('show');
            setTimeout(() => {
                if (modal.parentElement) {
                    // Call onClose callback if exists
                    const onClose = modal.dataset.onClose;
                    if (onClose && typeof window[onClose] === 'function') {
                        window[onClose]();
                    }
                    modal.remove();
                    this.activeModals.delete(id);
                }
            }, 300);
        }
    }

    // Hide all modals
    hideAll() {
        this.activeModals.forEach(id => {
            this.hide(id);
        });
    }

    // Get button class based on type
    getButtonClass(type) {
        const classes = {
            primary: 'modal-btn-primary',
            secondary: 'modal-btn-secondary',
            danger: 'modal-btn-danger',
            success: 'modal-btn-success',
            warning: 'modal-btn-warning'
        };
        return classes[type] || 'modal-btn-secondary';
    }

    // Show confirmation modal
    confirm(options) {
        const {
            title = 'Confirm Action',
            message = 'Are you sure you want to proceed?',
            confirmText = 'Confirm',
            cancelText = 'Cancel',
            type = 'warning',
            onConfirm = null,
            onCancel = null
        } = options;

        return this.show({
            id: 'confirm-modal',
            title,
            content: `<p>${message}</p>`,
            type,
            buttons: [
                {
                    text: cancelText,
                    type: 'secondary',
                    onClick: `modalSystem.hide('confirm-modal'); ${onCancel ? onCancel : ''}`
                },
                {
                    text: confirmText,
                    type: type === 'error' ? 'danger' : 'primary',
                    onClick: `modalSystem.hide('confirm-modal'); ${onConfirm ? onConfirm : ''}`
                }
            ]
        });
    }

    // Show alert modal
    alert(options) {
        const {
            title = 'Alert',
            message = '',
            type = 'info',
            buttonText = 'OK',
            onClose = null
        } = options;

        return this.show({
            id: 'alert-modal',
            title,
            content: `<p>${message}</p>`,
            type,
            buttons: [
                {
                    text: buttonText,
                    type: 'primary',
                    onClick: `modalSystem.hide('alert-modal'); ${onClose ? onClose : ''}`
                }
            ]
        });
    }

    // Show delete confirmation modal
    deleteConfirm(options) {
        const {
            itemType = 'item',
            onConfirm = null,
            onCancel = null
        } = options;

        return this.confirm({
            title: 'Confirm Deletion',
            message: `Are you sure you want to delete this ${itemType}? This action cannot be undone.`,
            confirmText: 'Delete',
            cancelText: 'Cancel',
            type: 'error',
            onConfirm,
            onCancel
        });
    }
}

// Initialize modal system
const modalSystem = new ModalSystem();

// Global functions for backward compatibility
function showModal(options) {
    return modalSystem.show(options);
}

function hideModal(id) {
    modalSystem.hide(id);
}

function showConfirmModal(options) {
    return modalSystem.confirm(options);
}

function showAlertModal(options) {
    return modalSystem.alert(options);
}

function showDeleteModal(itemType, onConfirm, onCancel) {
    return modalSystem.deleteConfirm({
        itemType,
        onConfirm,
        onCancel
    });
}
