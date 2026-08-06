/**
 * submit logout form
 * @var {HTMLFormElement} form
 */
export function submitLogoutForm() {
    const LogoutForm = document.getElementById('logoutForm') || false;
    const LogoutFormClickButton = document.getElementById('log-out-form-button') || false;
    if (LogoutFormClickButton && LogoutForm) {
        LogoutFormClickButton.addEventListener('click', () => {
            LogoutForm.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
        });
    }
}

/**
 * previewing an selected(uploaded) image
 * @param {HTMLFormElement} element
 */
export function previewSelectedImage(element) {
    if (element.files && element.files[0]) {
        const imgUrl = URL.createObjectURL(element.files[0]);
        const targetImg = document.querySelector(element.dataset.targetImg);
        if (targetImg) {
            targetImg.src = imgUrl;
        }
    }
}

/**
 * Show or hide a partial AJAX loader inside a container
 *
 * @param {string} selector - CSS selector for target elements
 * @param {boolean} loaded - true = remove loader, false = add loader
 */
import { createLoader } from "./ui/common";
export function partialLoadingAjax(selector, loaded) {
    const loader = createLoader().outerHTML;

    document.querySelectorAll(selector).forEach(e => {
        if (loaded) {
            const existing = e.querySelector('.ajax-loader-spinner');
            if (existing) setTimeout(() => {
                existing.remove();
            }, 200);
        } else {
            // Only add if not already present
            if (!e.querySelector('.ajax-loader-spinner')) {
                e.insertAdjacentHTML('beforeend', loader);
            }
        }
    });
}

/**
 * load data from server
 * @param {String} url server uri
 * @param {String} loader element identifier
 * @returns
 */
export async function load_data(url, loader = null) {
    loader && partialLoadingAjax(loader, false); // show loader
    try {
        const response = await fetch(url);

        if (!response.ok) {
            // show error modal with status text
            showToast('error', response.statusText);
            return null; // stop here if error
        }

        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error:', error);
        showToast('error', error.message);
        return null;
    } finally {
        // always hide loader, success or error
        loader && partialLoadingAjax(loader, true);
    }
}

/**
 * Safely set a field's value depending on its type.
 *
 * @param {HTMLElement} field - The form field element
 * @param {mixed} value - The value to set
 * @param {string} ts_map_key - Mapping key to TomSelect Value field
 */
export function setFieldValue(field, value, ts_map_key='id') {
    if (!field) return;

    const tag = field.tagName.toLowerCase();

    if (field.tomselect) {
        // If value is an array of objects → map to IDs
        if (Array.isArray(value)) {
            // Example: role.permissions → [{id:1,...}, {id:2,...}]
            const ids = value.map(item =>
                typeof item === 'object' ? item[ts_map_key] : item
            );
            field.tomselect.setValue(ids);
        } else {
            // Single value (string/number)
            field.tomselect.setValue(value);
        }
        return;
    }

    if (tag === 'textarea') {
        // Use .value for textarea content
        field.value = value ?? '';
        return;
    }

    if (tag === 'select') {
        if (field.multiple && Array.isArray(value)) {
            Array.from(field.options).forEach(opt => {
                opt.selected = value.includes(opt.value);
            });
        } else {
            field.value = value ?? '';
        }
        return;
    }

    switch (field.type) {
        case 'checkbox':
            field.checked = Boolean(value) && (value === true || value == field.value);
            break;

        case 'radio':
            field.checked = (field.value == value);
            break;

        case 'file':
            // Skip file inputs (cannot set programmatically)
            break;

        default:
            field.value = value ?? '';
            break;
    }
}

/**
 * bootstrap modal backdrop cleanup
 */
export function cleanupModalBackdrop() {
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('padding-right');
    document.querySelectorAll('.modal-backdrop').forEach((backdrop) => backdrop.remove());
}

/**
 * create bootstrap modal, with backdrop clear attached.
 * @param {HTMLElement} modalElement
 * @returns
 */
import { Modal } from "bootstrap";
export function createBootstrapModal(modalElement) {
    if (!modalElement) return null;

    const modal = Modal.getOrCreateInstance(modalElement);
    modalElement.addEventListener('hidden.bs.modal', () => {
        cleanupModalBackdrop();
    }, { once: true });

    return modal;
}


// studentActions.js

// Tracks whether the table is showing active or deleted records
export let dt_show_deleted_records = 'active'; // deleted | active | all

/** DataTable-Utility
 * Set the current deleted/active state and refresh the table.
 * Also updates the Delete/Restore button and highlights the correct view button.
 * @param {string} state - 'active' or 'deleted'
 * @param {object} table - DataTable instance
 */
export function DT_setDeletedState(state, table) {
    dt_show_deleted_records = state;
    table.ajax.reload();
    DT_updateDeleteRestoreButton(table);
    DT_updateViewButtons(table);
}

/** DataTable-Utility
 * Update the Delete/Restore button text and color based on current state.
 * If viewing deleted records, the button shows "Restore" in green.
 * Otherwise, it shows "Delete" in red.
 * @param {object} table - DataTable instance
 */
export function DT_updateDeleteRestoreButton(table) {
    const btnApi = table.button('.btn-delete-restore');
    if (!btnApi) {
        console.warn('Delete/Restore button not found');
        return;
    }

    // .node() may return a jQuery object, unwrap to DOM element
    const btnNode = btnApi.node();
    const btnEl = btnNode instanceof HTMLElement ? btnNode : btnNode[0];

    if (btnEl) {
        if (dt_show_deleted_records === 'deleted') {
            btnApi.text('Restore');
            btnEl.classList.remove('btn-danger');
            btnEl.classList.add('btn-success');
        } else {
            btnApi.text('Delete');
            btnEl.classList.remove('btn-success');
            btnEl.classList.add('btn-danger');
        }
    }
}

/** DataTable-Utility
 * Highlight the correct "View" sub-button (Active or Deleted).
 * Ensures the UI reflects which dataset is currently being shown.
 * @param {object} table - DataTable instance
 */
export function DT_updateViewButtons(table) {
    const activeBtnApi = table.button('.buttons-active');
    const deletedBtnApi = table.button('.buttons-deleted');

    if (!activeBtnApi || !deletedBtnApi) {
        console.warn('View buttons not found');
        return;
    }

    // unwrap jQuery object to DOM element
    const activeBtn = activeBtnApi.node();
    const deletedBtn = deletedBtnApi.node();

    const activeEl = activeBtn instanceof HTMLElement ? activeBtn : activeBtn[0];
    const deletedEl = deletedBtn instanceof HTMLElement ? deletedBtn : deletedBtn[0];

    // first child is the actual button

    if (dt_show_deleted_records === 'active') {
        activeEl.firstChild.classList.add('active');
        deletedEl.firstChild.classList.remove('active');
    } else {
        deletedEl.firstChild.classList.add('active');
        activeEl.firstChild.classList.remove('active');
    }
}

/** DataTable-Utility
 * Handle delete/restore action for a selected student row.
 * Shows a confirmation modal, then submits an AJAX form to the server.
 * After success, reloads the DataTable.
 * @param {object} table - DataTable instance
 * @param {string} action - form action url
 * @param {string} message - message to display on confirm modal
 */
import { showConfirmModal } from "./message_models";
import { submitAjaxForm } from "./ajax";
export function DT_handleDeleteRestore(table, action, message) {
    showConfirmModal(message, () => {
        const form = document.createElement('form');
        form.action = action;
        form.method = 'delete';
        form.setAttribute('data-method', 'delete');

        form.addEventListener('ajax:success', () => {
            table.ajax.reload(null, false);
        }, { once: true });

        submitAjaxForm(form);
    });
}
