import { clearFormErrors } from "./ajax";
import { load_data, setFieldValue, cleanupModalBackdrop, createBootstrapModal, DT_handleDeleteRestore, DT_setDeletedState, dt_show_deleted_records } from "./utility";
import { renderProfileRow } from './ui/dt-render-profile-row';
import { createActionRow } from "./ui/common";

/**
 * DT imports
 */
import DataTable from 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';
import 'datatables.net-select-bs5';

// identifiers NEW GUARDIAN modal
const GUARDIAN_NEW_MODAL = '#new-guardian-model';
const GUARDIAN_NEW_MODAL_TRIGGER = '#new-guardian-add-model-init-btn';
const GUARDIAN_NEW_MODAL_FORM = '#new-edit-guardian-form';
const GUARDIAN_VIEW_MODAL = '#view-guardian-model';
const GUARDIAN_VIEW_MODAL_STUDENTS_LIST = '#guardian-students-list-display';

// data table
const GUARDIAN_DT_INDEX_TABLE = '#dt-guardians-index-table'; // table

// variables
let DTable;

// inits
document.addEventListener('DOMContentLoaded', () => {
    // triggerNewGuardianModal();
    formEventsTracker();
    // init dt
    DTable = initIndexDT();
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

        // reload the dt
        DTable.ajax.reload();

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
    const response = await load_data(ROUTES.guardians_show.replace(':guardian_code', guardianCode), GUARDIAN_NEW_MODAL);
    const guardian = response?.guardian || response?.data?.guardian || {};
    if (!guardian) {
        console.log('Error Loading Data for student view modal');
        return false;
    }
    // guardian data fill
    for (const [key, value] of Object.entries(guardian)) {
        modal.querySelectorAll(`[name="${key}"], #${key}`).forEach(field => {
            setFieldValue(field, value);
        });
    }
    // setup cover image
    const coverImgPreview = modal.querySelector('img#guardian__cover_img-preview');
    if (coverImgPreview) coverImgPreview.src = guardian.guardian__cover_img_url;

    return response;
}

/**
 * open view modal and assign data to its fields
 * @param {string} guardianCode
 */
async function openGuardianViewModal(guardianCode) {
    const ModalElement = document.querySelector(GUARDIAN_VIEW_MODAL);
    const viewModal = createBootstrapModal(ModalElement);

    if (!ModalElement || !viewModal) {
        console.error('Guardian view modal elements not found');
        return null;
    }

    // load data
    const response = await load_data(ROUTES.guardians_show.replace(':guardian_code', guardianCode), GUARDIAN_VIEW_MODAL);

    const guardian = response?.guardian || response?.data?.guardian || {};
    const students = response?.students || response?.data?.students || {};
    if (!guardian) {
        console.log('Error Loading Data for guardian view modal');
        return false;
    }
    // guardian data fill
    for (const [key, value] of Object.entries(guardian)) {
        ModalElement.querySelectorAll(`[name="${key}"], #${key}`).forEach(field => {
            setFieldValue(field, value);
        });
    }
    // setup cover image
    const coverImgPreview = ModalElement.querySelector('img#guardian__cover_img-preview');
    if (coverImgPreview) coverImgPreview.src = guardian.guardian__cover_img_url;

    // render students list data
    const studentsListRender = document.querySelector(GUARDIAN_VIEW_MODAL_STUDENTS_LIST);
    if (studentsListRender) {
        studentsListRender.innerHTML = null;
        for (const [key, value] of Object.entries(students)) {
            studentsListRender.appendChild(renderProfileRow(value.student__cover_img_url, [value.student__name, '#'+value.student__student_code]))
        }
    }

    // open modal
    cleanupModalBackdrop();
    viewModal.show();
}

/**
 * data table
 */
function initIndexDT() {
    const table = new DataTable(GUARDIAN_DT_INDEX_TABLE, {
        processing: true,
        serverSide: true,
        ajax: {
            url: ROUTES.guardians_dt_index,
            data: (d) => {
                d.showDeleted = dt_show_deleted_records; // custom param
            }
        },
        columns: [
            { data: null, defaultContent: '', className: 'select-checkbox', orderable: false },
            {
                data: 'guardian__name',
                name: 'guardians.name',
                orderable: true, searchable: true,
                render: (_, __, row) => renderProfileRow(
                    row.guardian__cover_img_url,
                    [row.guardian__name, '#' + row.guardian__guardian_code]
                )
            },
            { data: 'guardian__tel', name: 'guardians.tel', orderable: true, searchable: true },
            { data: 'guardian__nic', name: 'nic', orderable: true, searchable: true },
        ],
        layout: {
            topStart: {
                buttons: [
                    { text: 'New', className: 'btn-primary', action: () => setupGuardianModal('new') },
                    {
                        text: 'View', className: 'btn-info dt-action-btn', action: () => {
                            const selected = table.rows({ selected: true }).data();
                            if (selected.length) openGuardianViewModal(selected[0].guardian__guardian_code);
                        }
                    },
                    {
                        text: 'Edit', className: 'btn-info dt-action-btn', action: () => {
                            const selected = table.rows({ selected: true }).data();
                            if (selected.length) setupGuardianModal('edit', selected[0].guardian__guardian_code);
                        }
                    },
                    {
                        text: 'Delete',
                        className: 'btn-danger btn-delete-restore dt-action-btn',
                        action: () => {
                            const selected = table.rows({ selected: true }).data();
                            if (selected.length) {
                                handleDeleteRestore(selected[0], table);
                            }
                        }
                    },
                    {
                        extend: 'collection',
                        text: 'Records Filter',
                        buttons: [
                            {
                                text: 'Active Records',
                                className: 'buttons-active',
                                action: () => DT_setDeletedState('active', table)
                            },
                            {
                                text: 'Deleted Records',
                                className: 'buttons-deleted',
                                action: () => DT_setDeletedState('deleted', table)
                            }
                        ]
                    },
                ]
            }
        },
        select: {
            style: 'single',
            selector: 'td.select-checkbox'
        },
        order: [[1, 'asc']]
    });

    return table;
}

/**
 * submit delete form guardians
 * @param {object} row - row data
 * @param {Object} table - data table
 */
function handleDeleteRestore(row, table) {
    const actionText = row.guardian__is_deleted ? "Restore" : "Delete";
    const message = `Confirm ${actionText} guardian: ${row.guardian__name}?`;
    const action = ROUTES.guardians_destroy.replace(':guardian_code', row.guardian__guardian_code);
    DT_handleDeleteRestore(table, action, message)
}

