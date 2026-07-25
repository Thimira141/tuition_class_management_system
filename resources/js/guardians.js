import { clearFormErrors } from "./ajax";
import { load_data, setFieldValue, cleanupModalBackdrop, createBootstrapModal } from "./utility";

// identifiers NEW GUARDIAN modal
const GUARDIAN_NEW_MODAL = '#new-guardian-model';
const GUARDIAN_NEW_MODAL_TRIGGER = '#new-guardian-add-model-init-btn';
const GUARDIAN_NEW_MODAL_FORM = '#new-edit-guardian-form';

// variables

// inits
document.addEventListener('DOMContentLoaded', () => {
    triggerNewGuardianModal();
    formEventsTracker();
});

// reusable functions

export function formEventsTracker(options = {}) {
    const guardianForm = document.querySelector(GUARDIAN_NEW_MODAL_FORM);
    if (!guardianForm) {
        console.error('Forms not found to listen');
        return;
    }

    guardianForm.addEventListener('ajax:success', (event) => {
        const responseData = event.detail || {};
        const guardianCode = responseData?.data?.guardian__guardian_code || responseData?.guardian__guardian_code || null;
        const guardianId = responseData?.data?.guardian__id || responseData?.guardian__id || null;

        if (guardianCode && guardianForm.method !== 'put') {
            guardianForm.method = 'put';
            guardianForm.action = ROUTES.guardians_update.replace(':guardian_code', guardianCode);
        }

        if (guardianId && typeof options.onGuardianCreated === 'function') {
            options.onGuardianCreated(guardianId);
        }
    });
}

/**
 * attach the trigger for new guardian button event
 */
export function triggerNewGuardianModal() {
    const triggerBtn = document.querySelector(GUARDIAN_NEW_MODAL_TRIGGER);

    if (!triggerBtn) {
        console.error('Guardian modal trigger not found');
        return;
    }

    triggerBtn.addEventListener('click', (event) => {
        event.preventDefault();
        setupGuardianModal('new');
    });
}


/**
 * Setup the guardian modal for new or edit modes
 * @param {string} mode 'new' | 'edit'
 * @param {string|number|null} guardianCode
 */
async function setupGuardianModal(mode, guardianCode = null) {
    const guardianModalForm = document.querySelector(GUARDIAN_NEW_MODAL_FORM);
    const guardianModalElement = document.querySelector(GUARDIAN_NEW_MODAL);
    const guardianModal = createBootstrapModal(guardianModalElement);

    if (!guardianModalForm || !guardianModalElement || !guardianModal) {
        console.error('Guardian modal elements not found');
        return null;
    }

    // reset form
    guardianModalForm.reset();
    clearFormErrors(guardianModalForm);

    if (mode === 'new') {
        guardianModalForm.action = ROUTES.guardians_store;
        guardianModalForm.method = 'post';
    } else if (mode === 'edit') {
        guardianModalForm.action = ROUTES.guardians_update.replace(':guardian_code', guardianCode);
        guardianModalForm.method = 'put';
        await loadGuardianData(guardianCode, guardianModalForm, guardianModalElement);
    }

    // open modal
    cleanupModalBackdrop();
    guardianModal.show();
}

/**
 *
 * @param {String|Number} guardianCode
 * @param {HTMLFormElement} form
 * @param {HTMLElement} modal
 * @returns
 */
async function loadGuardianData(guardianCode, form, modal) {
    // get data from server
    const response = await load_data(ROUTES.guardians_show.replace(':guardian_code', guardianCode), modal);
    // fill form fields
    for (const [key, value] of Object.entries(response.user)) {
        modal.querySelectorAll(`[name="${key}"], #${key}`).forEach(field => {
            setFieldValue(field, value);
            // attach function to show/hide new-edit modal -> form -> staff_deps select
            // const _child = document.querySelector(field.getAttribute('data-child'));
            // const _wrapper = document.querySelector(field.getAttribute('data-wrapper'));
            // if (_child || _wrapper) {
            //     toggleChildVisibilityByParentValue(_child, _wrapper, value); // todo run tests
            // }
        });
    }

    return response;
}
