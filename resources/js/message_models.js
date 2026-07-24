import { Modal, Toast } from 'bootstrap';
import { cleanupModalBackdrop, createBootstrapModal } from "./utility";

export function showToast(type, message, alert = false, alertType = 'danger') {
    const toastEl = document.getElementById('app-toast');
    const toastBody = toastEl.querySelector('.toast-body');

    // Update classes
    toastEl.className = `toast align-items-center text-bg-${type} border-0`;

    // Main message
    toastBody.textContent = message;

    // Optional alert content (append below message)
    if (alert) {
        const alertEl = document.createElement('div');
        alertEl.className = `alert alert-${alertType} mt-2 mb-0 p-2`;
        alertEl.textContent = alert;
        toastBody.appendChild(alertEl);
    }

    // Show toast
    const toast = new Toast(toastEl);
    toast.show();
}

export function showConfirmModal(message, onConfirm) {
    const modalBody = document.getElementById('confirmModalBody');
    modalBody.textContent = message;

    const modalElement = document.getElementById('confirmModal');
    const confirmModal = createBootstrapModal(modalElement);

    const proceedBtn = document.getElementById('confirmModalProceed');
    const handler = () => {
        onConfirm();
        confirmModal.hide();
    };
    proceedBtn.addEventListener('click', handler, { once: true });

    cleanupModalBackdrop();
    confirmModal.show();
}
