/**
 *
 * @returns HTMLDivElement
 */
/**
 * Create a view button
 * @param {Object} ActionRowData
 * @returns HTMLButtonElement
 */
function createViewButton(ActionRowData) {
    const viewBtn = document.createElement('button');
    viewBtn.className = 'btn btn-sm btn-info';
    viewBtn.type = 'button';
    viewBtn.onclick = () => {
        if (ActionRowData.view_btn_action && typeof ActionRowData.view_btn_action === 'function') {
            ActionRowData.view_btn_action();
        }
    };

    const viewIcon = document.createElement('i');
    viewIcon.className = 'bi bi-box-arrow-up-right me-1';

    const viewText = document.createElement('span');
    viewText.className = 'd-none d-md-inline';
    viewText.textContent = 'View';

    viewBtn.appendChild(viewIcon);
    viewBtn.appendChild(viewText);
    return viewBtn;
}

/**
 * Create a delete/restore button
 * @param {Object} ActionRowData
 * @returns HTMLButtonElement
 */
function createDeleteRestoreButton(ActionRowData) {
    const dr_btn = document.createElement('button');
    dr_btn.type = 'button';

    if (ActionRowData.delete_btn_dataset && typeof ActionRowData.delete_btn_dataset === 'object') {
        for (const [key, value] of Object.entries(ActionRowData.delete_btn_dataset)) {
            dr_btn.setAttribute(`data-${key}`, value);
        }
    }

    dr_btn.onclick = () => {
        if (ActionRowData.delete_btn_action && typeof ActionRowData.delete_btn_action === 'function') {
            ActionRowData.delete_btn_action(dr_btn);
        }
    };

    const dr_btn_Icon = document.createElement('i');
    const dr_btn_text = document.createElement('span');
    dr_btn_text.className = 'd-none d-md-inline';

    if (ActionRowData.is_deleted == '1') {
        dr_btn.className = 'btn btn-sm btn-success';
        dr_btn_Icon.className = 'bi bi-arrow-counterclockwise';
        dr_btn_text.textContent = 'Restore';
    } else {
        dr_btn.className = 'btn btn-sm btn-danger';
        dr_btn_Icon.className = 'bi bi-trash3-fill me-1';
        dr_btn_text.textContent = 'Delete';
    }

    dr_btn.appendChild(dr_btn_Icon);
    dr_btn.appendChild(dr_btn_text);
    dr_btn.setAttribute('data-delete-form-confirm-msg',
        `Are sure want to ${dr_btn_text.textContent} this Record ID: ${ActionRowData.public_id} ?`);

    return dr_btn;
}

/**
 * Create action row with view and delete/restore buttons
 * @param {Object} ActionRowData
 * @returns HTMLDivElement
 */
export function createActionRow(ActionRowData) {
    const row = document.createElement('div');
    row.className = 'row';

    if (ActionRowData.viewPermission) {
        const colView = document.createElement('div');
        colView.className = 'col-auto mx-1 px-0';
        colView.appendChild(createViewButton(ActionRowData));
        row.appendChild(colView);
    }

    if (ActionRowData.deletePermission) {
        const colDeleteRestore = document.createElement('div');
        colDeleteRestore.className = 'col-auto mx-1 px-0';
        colDeleteRestore.dataset.deleted_status = ActionRowData.is_deleted;
        colDeleteRestore.appendChild(createDeleteRestoreButton(ActionRowData));
        row.appendChild(colDeleteRestore);
    }

    return row;
}

/**
 * create a simple loader
 * @returns HTMLDivElement
 */
export function createLoader() {
    // Outer wrapper
    const wrapper = document.createElement('div');
    wrapper.className = 'position-absolute top-0 end-0 w-100 h-100 z-3 d-flex justify-content-center align-items-center ajax-loader-spinner';
    wrapper.style.backdropFilter = 'blur(1px)';

    // Spinner element
    const spinner = document.createElement('div');
    spinner.className = 'spinner-border text-primary spinner-border-sm';
    spinner.setAttribute('role', 'status');

    // Append spinner to wrapper
    wrapper.appendChild(spinner);

    return wrapper;
}
