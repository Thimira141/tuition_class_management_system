import TomSelect from "tom-select";
import { clearFormErrors } from "./ajax";
import { load_data, setFieldValue, cleanupModalBackdrop, createBootstrapModal, DT_handleDeleteRestore, DT_setDeletedState, dt_show_deleted_records } from "./utility";
import { formEventsTracker as guardian_formEventsTracker, triggerNewGuardianModal } from "./guardians";
import { renderProfileRow } from './ui/dt-render-profile-row';
import { createActionRow } from "./ui/common";

/**
 * DT imports
 */
import DataTable from 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';
import 'datatables.net-select-bs5';

// identifiers for NEW STUDENT modal
const STUDENT_NEW_MODAL = '#new-student-model';
const STUDENT_NEW_MODAL_TRIGGER = '#new-student-add-model-init-btn';
const STUDENT_NEW_MODAL_FORM = '#new-edit-student-form';
const STUDENT_NEW_MODAL_GUARDIAN_SELECT = '#new-student-model #student__guardian_id';
const STUDENT_DELETE_FORM = '#delete-student-form';

// NEW GUARDIAN modal
const GUARDIAN_NEW_MODAL = '#new-guardian-model';
const GUARDIAN_NEW_MODAL_TRIGGER = '#new-guardian-add-model-init-btn';
const GUARDIAN_NEW_MODAL_FORM = '#new-edit-guardian-form';

// data table
const STUDENT_DT_INDEX_TABLE = '#dt-students-index-table'; // table
const STUDENT_DT_INDEX_Q_INPUT = '#dt-student-index-q-input'; // search field

// variables
let DTable;

// NOTE: 'student__', 'guardian__' is prefix used to match DB aliases in server-side queries

// inits
document.addEventListener('DOMContentLoaded', () => {
    // init guardians select in new,edit form
    const studentGuardianSelect = initStudentGuardianSelect();
    // init new,edit model actions
    triggerNewStudentModal();
    triggerNewGuardianModal(); // for guardian modal

    formEventsTracker(studentGuardianSelect);
    DTable = initIndexDT();
    DT_setDeletedState('active', DTable);
});

// reusable functions

/**
 *
 */
function formEventsTracker(studentGuardianSelect) {
    const studentForm = document.querySelector(STUDENT_NEW_MODAL_FORM);
    const guardianForm = document.querySelector(GUARDIAN_NEW_MODAL_FORM);
    if (!studentForm || !guardianForm) {
        console.error('Forms not found to listen');
        return;
    }

    guardian_formEventsTracker({
        onGuardianCreated: (guardianId) => {
            if (guardianId && studentGuardianSelect) {
                studentGuardianSelect.setValue(guardianId);
            }
        }
    });

    // change form action, method
    studentForm.addEventListener('ajax:success', (event) => {
        const responseData = event.detail || {};
        const studentCode = responseData?.data?.student__student_code || responseData?.student__student_code || null;

        if (studentCode && studentForm.method !== 'put') {
            studentForm.method = 'put';
            studentForm.action = ROUTES.students_update.replace(':student_code', studentCode);
        }
    });
}

/**
 * attach the trigger for new student button event
 */
function triggerNewStudentModal() {
    const triggerBtn = document.querySelector(STUDENT_NEW_MODAL_TRIGGER);

    if (!triggerBtn) {
        console.error('Student modal trigger not found');
        return;
    }

    triggerBtn.addEventListener('click', (event) => {
        event.preventDefault();
        setupStudentModal('new');
    });
}

/**
 * tom-select, for guardian select
 * @returns {TomSelect}
 */
function initStudentGuardianSelect() {
    return new TomSelect(STUDENT_NEW_MODAL_GUARDIAN_SELECT, {
        create: false,
        valueField: "id",
        labelField: "name",
        searchField: "name", // todo display <name>-<nic>
        load: function (query, callback) {
            fetch(ROUTES.guardians_ts + "?q=" + encodeURIComponent(query))
                .then(response => response.json())
                .then(json => callback(json))
                .catch(() => callback());
        }
    });
}

/**
 * Setup the student modal for new or edit modes
 * @param {string} mode 'new' | 'edit'
 * @param {string|number|null} studentCode
 */
async function setupStudentModal(mode, studentCode = null) {
    const studentModalForm = document.querySelector(STUDENT_NEW_MODAL_FORM);
    const studentModalElement = document.querySelector(STUDENT_NEW_MODAL);
    const studentModal = createBootstrapModal(studentModalElement);

    if (!studentModalForm || !studentModalElement || !studentModal) {
        console.error('Student modal elements not found');
        return null;
    }

    // reset form
    studentModalForm.reset();
    clearFormErrors(studentModalForm);

    if (mode === 'new') {
        studentModalForm.action = ROUTES.students_store;
        studentModalForm.method = 'post';
    } else if (mode === 'edit') {
        studentModalForm.action = ROUTES.students_update.replace(':student_code', studentCode);
        studentModalForm.method = 'put';
        await loadStudentData(studentCode, studentModalForm, studentModalElement);
    }

    // open modal
    cleanupModalBackdrop();
    studentModal.show();
}

/**
 *
 * @param {String|Number} studentCode
 * @param {HTMLFormElement} form
 * @param {HTMLElement} modal
 * @returns
 */
async function loadStudentData(studentCode, form, modal) {
    // get data from server
    const response = await load_data(ROUTES.students_show.replace(':student_code', studentCode), modal);
    // fill form fields
    for (const [key, value] of Object.entries(response.user)) {
        modal.querySelectorAll(`[name="${key}"], #${key}`).forEach(field => {
            setFieldValue(field, value);
        });
    }

    return response;
}

/**
 * init data table
 */
function initIndexDT() {
    const table = new DataTable(STUDENT_DT_INDEX_TABLE, {
        processing: true,
        serverSide: true,
        ajax: {
            url: ROUTES.students_dt_index,
            data: (d) => {
                d.showDeleted = dt_show_deleted_records; // custom param
            }
        },
        columns: [
            { data: null, defaultContent: '', className: 'select-checkbox', orderable: false },
            {
                data: 'student__name',
                name: 'students.name',
                orderable: true, searchable: true,
                render: (_, __, row) => renderProfileRow(
                    row.student__cover_img,
                    [row.student__name, '#' + row.student__student_code]
                )
            },
            { data: 'student__tel', name: 'students.tel', orderable: true, searchable: true },
            { data: 'student__dob', name: 'students.dob', orderable: true, searchable: true },
        ],
        layout: {
            topStart: {
                buttons: [
                    { text: 'New', className: 'btn-primary', action: () => setupStudentModal('new') },
                    {
                        text: 'View', className: 'btn-info dt-action-btn', action: () => {
                            const selected = table.rows({ selected: true }).data();
                            if (selected.length) console.log('View', selected[0].student__student_code);
                        }
                    },
                    {
                        text: 'Edit', className: 'btn-info dt-action-btn', action: () => {
                            const selected = table.rows({ selected: true }).data();
                            if (selected.length) setupStudentModal('edit', selected[0]);
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
                        text: 'View',
                        buttons: [
                            {
                                text: 'Active',
                                className: 'buttons-active',
                                action: () => DT_setDeletedState('active', table)
                            },
                            {
                                text: 'Deleted',
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
 * submit delete form students
 * @param {object} row - row data
 * @param {Object} table - data table
 */
function handleDeleteRestore(row, table) {
    const actionText = row.student__is_deleted ? "Restore" : "Delete";
    const message = `Confirm ${actionText} Student: ${row.student__name}?`;
    const action = ROUTES.students_destroy.replace(':student_code', row.student__student_code);
    DT_handleDeleteRestore(table, action, message)
}


