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
 * Handle DataTable record deletion via form dispatch.
 *
 * @param {DataTable} dt - DataTable instance
 * @param {HTMLButtonElement} deleteBtn - Button triggering delete
 * @returns {null|void}
 */
export function handleDTDeleteRecord(dt, deleteBtn) {
    // get delete form
    const deleteForm = document.querySelector(deleteBtn.getAttribute('data-target-delete-form')) || false;
    if (!deleteForm) {
        console.error('Delete From Not Found!');
        return null;
    }
    // set delete form action
    deleteForm.setAttribute('action', deleteBtn.getAttribute('data-delete-form-action'));
    // set table selector value
    const deleteFromTBS = deleteForm.querySelector('input#table-selector') || false;
    if (deleteFromTBS) deleteFromTBS.value = deleteBtn.getAttribute('data-table-selector');
    // set confirm message
    const deleteFormConfirmMsg = deleteBtn.getAttribute('data-delete-form-confirm-msg');
    if (deleteFormConfirmMsg) {
        deleteForm.setAttribute('data-confirm-message', deleteFormConfirmMsg);
        deleteForm.setAttribute('data-confirm', 'true');
    } else {
        deleteForm.setAttribute('data-confirm', 'false');
    }
    // submit form
    deleteForm.dispatchEvent(new Event('submit', { bubbles: true }));
    // form event -> ajax:success then reload dt; don't repeat the event
    if (!deleteForm._ajaxBound) {
        deleteForm.addEventListener('ajax:success', () => {
            dt.ajax.reload();
        });
        deleteForm._ajaxBound = true; // mark as event bound
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
