import axios from "axios";
import { showToast, showConfirmModal } from "./message_models";

export default function initAjaxForms() {
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('form.ajax-form').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                clearFormErrors(form);

                // If confirmation is required
                if (form.dataset.confirm === "true") {
                    const message = form.dataset.confirmMessage || "Are you sure?";
                    showConfirmModal(message, () => {
                        submitAjaxForm(form);
                    });
                } else {
                    submitAjaxForm(form);
                }
            });

            // add a reset image event too...
            form.addEventListener('reset', () => {
                resetFormImages(form);
                // reset form
                form.reset();
                // reset tom-select
                form.querySelectorAll('select').forEach(element => {
                    if (element.tomselect) {
                        element.tomselect.clear();
                    }
                });
            });
        });
    });
}

async function submitAjaxForm(form) {
    const loader = document.getElementById('page-loader');
    if (loader) loader.classList.remove('d-none');

    const formData = new FormData(form);
    const method = (form.getAttribute('data-method') || form.getAttribute('method') || form.method || 'get').toLowerCase();

    if (method === 'put' || method === 'patch' || method === 'delete') {
        formData.append('_method', method);
    }

    try {
        let response;
        if (method === 'post') {
            response = await axios.post(form.action, formData);
        } else if (method === 'put') {
            response = await axios.post(form.action, formData, {
                headers: { 'X-HTTP-Method-Override': 'PUT' }
            });
        } else if (method === 'patch') {
            response = await axios.post(form.action, formData, {
                headers: { 'X-HTTP-Method-Override': 'PATCH' }
            });
        } else if (method === 'delete') {
            response = await axios.post(form.action, formData, {
                headers: { 'X-HTTP-Method-Override': 'DELETE' }
            });
        } else {
            response = await axios.get(form.action, { params: Object.fromEntries(formData) });
        }

        const data = response?.data || {};
        const isSuccess = response && response.status >= 200 && response.status < 300;

        if (isSuccess) {
            showToast('success', data.message || 'Action completed successfully!', data?.error || false);
            if (data.redirect) {
                setTimeout(() => window.location.href = data.redirect, 1500);
            }
            form.dispatchEvent(new CustomEvent('ajax:success', { detail: data }));
            console.debug('[form-ajax:dispatch] ajax:success dispatched');
        } else {
            const errorBag = data.errors || data.errorBag || {};
            showToast('danger', data.message || 'Something went wrong.', data?.error || false);
            if (Object.keys(errorBag).length) {
                showInvalidateData(form, errorBag);
            }
            form.dispatchEvent(new CustomEvent('ajax:fails', { detail: data }));
            console.debug('[form-ajax:dispatch] ajax:fails dispatched');
        }
    } catch (error) {
        const responseData = error?.response?.data || {};
        const errorBag = responseData.errors || responseData.errorBag || {};
        const message = responseData.message || error?.message || 'Something went wrong.';

        showToast('danger', message, responseData?.error || false);
        if (Object.keys(errorBag).length) {
            showInvalidateData(form, errorBag);
        }

        form.dispatchEvent(new CustomEvent('ajax:error', { detail: responseData }));
        console.debug('[form-ajax:dispatch] ajax:error dispatched');
    } finally {
        if (loader) loader.classList.add('d-none');
        form.dispatchEvent(new CustomEvent('ajax:complete'));
        console.debug('[form-ajax:dispatch] ajax:complete dispatched');
    }
}

export function clearFormErrors(form) {
    form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}

function showInvalidateData(form, errorBag) {
    Object.keys(errorBag).forEach(field => {
        let input = form.querySelector(`[name="${field}"], [id="${field}"]`) || false;
        if (!input) {
            let _field = field.split('.')[0]
            input = form.querySelector(`[name="${_field}"], [id="${_field}"]`)
        }
        console.debug('input:', input);
        if (input) {
            input.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback w-100';
            errorDiv.innerHTML = errorBag[field].join('<br>');
            input.insertAdjacentElement('afterend', errorDiv);
        }
    });
}

/**
 * reset the images that have reset request
 * @param {HTMLFormElement} form
 */
function resetFormImages(form) {
    form.querySelectorAll('img[data-image-reset]').forEach(img => {
        const key = img.dataset.imageReset; // "appRoutes.userImagePlaceholder"

        // Resolve the value from window
        const parts = key.split('.');
        let value = window;
        for (const part of parts) {
            if (value && part in value) {
                value = value[part];
            } else {
                value = null;
                break;
            }
        }

        if (value) {
            img.dataset.imageReset = value; // replace with actual URL
        }

        // reset the image url
        img.src = img.dataset.imageReset;

        console.debug('Reset From Selected Images');
    });

}
