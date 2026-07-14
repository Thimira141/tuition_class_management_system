import { Modal } from 'bootstrap';

export function showModal(type, message, alert = false, alertType = 'danger') {
    const modalBody = document.getElementById('feedbackModalBody');
    const modalTitle = document.getElementById('feedbackModalLabel');
    const modalMessage = modalBody.querySelector('p#message');
    const modalAlertContent = modalBody.querySelector('p#alertContent');

    modalTitle.textContent = type === 'success' ? 'Success' : 'Error';
    modalTitle.className = 'modal-title text-' + type;

    modalMessage.textContent = message;
    modalMessage.className = 'modal-body text-' + type;

    if (alert) {
        modalAlertContent.textContent = alert;
        modalAlertContent.className = 'alert alert-' + alertType;
    } else {
        modalAlertContent.className = 'd-none';
    }

    const feedbackModal = new Modal(document.getElementById('feedbackModal'));
    feedbackModal.show();
}

export function showConfirmModal(message, onConfirm) {
    const modalBody = document.getElementById('confirmModalBody');
    modalBody.textContent = message;

    const modalElement = document.getElementById('confirmModal');
    const confirmModal = new Modal(modalElement);

    const proceedBtn = document.getElementById('confirmModalProceed');
    const handler = () => {
        onConfirm();
        confirmModal.hide();
    };
    proceedBtn.addEventListener('click', handler, { once: true });

    confirmModal.show();
}
