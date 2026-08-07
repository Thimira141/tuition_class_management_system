import { clearFormErrors } from "./ajax";
import { load_data, setFieldValue, cleanupModalBackdrop, createBootstrapModal, DT_handleDeleteRestore, DT_setDeletedState, dt_show_deleted_records } from "./utility";
import { renderProfileRow } from './ui/dt-render-profile-row';
import { createActionRow } from "./ui/common";
import { openClassroomStudentsModal } from "./classroom_students";

/**
 * DT imports
 */
import DataTable from 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';
import 'datatables.net-select-bs5';

// identifiers NEW CLASSROOM modal
const CLASSROOM_NEW_MODAL = '#new-classroom-model';
const CLASSROOM_NEW_MODAL_TRIGGER = '#new-classroom-add-model-init-btn';
const CLASSROOM_NEW_MODAL_FORM = '#new-edit-classroom-form';
const CLASSROOM_VIEW_MODAL = '#view-classroom-model';
const CLASSROOM_VIEW_MODAL_STUDENTS_LIST = '#classroom-students-list-display';

// data table
const CLASSROOM_DT_INDEX_TABLE = '#dt-classrooms-index-table'; // table

// variables
let DTable;

// NOTE classroom__ is prefix, classes is table name

// inits
document.addEventListener('DOMContentLoaded', () => {
    // triggerNewClassroomModal();
    formEventsTracker();
    // // init dt
    DTable = initIndexDT();
    DT_setDeletedState('active', DTable);
});

/**
 *
 * @param {object} options
 * @returns
 */
function formEventsTracker(options = {}) {
    const classroomForm = document.querySelector(CLASSROOM_NEW_MODAL_FORM);
    if (!classroomForm) {
        console.error('Forms not found to listen');
        return;
    }

    classroomForm.addEventListener('ajax:success', (event) => {
        const responseData = event.detail || {};
        const classroomCode = responseData?.data?.classroom__classroom_code || responseData?.classroom__classroom_code || null;
        const classroomId = responseData?.data?.classroom__id || responseData?.classroom__id || null;

        if (classroomCode && classroomForm.method !== 'put') {
            classroomForm.method = 'put';
            classroomForm.action = ROUTES.classrooms_update.replace(':classroom_code', classroomCode);
        }

        // reload the dt
        DTable.ajax.reload();
    });
}

/**
 * Setup the classroom modal for new or edit modes
 * @param {string} mode 'new' | 'edit'
 * @param {string|number|null} classroomCode
 */
async function setupClassroomModal(mode, classroomCode = null) {
    const classroomModalForm = document.querySelector(CLASSROOM_NEW_MODAL_FORM);
    const classroomModalElement = document.querySelector(CLASSROOM_NEW_MODAL);
    const classroomModal = createBootstrapModal(classroomModalElement);

    if (!classroomModalForm || !classroomModalElement || !classroomModal) {
        console.error('Classroom modal elements not found');
        return null;
    }

    // reset form
    classroomModalForm.reset();
    clearFormErrors(classroomModalForm);

    if (mode === 'new') {
        classroomModalForm.action = ROUTES.classrooms_store;
        classroomModalForm.method = 'post';
    } else if (mode === 'edit') {
        classroomModalForm.action = ROUTES.classrooms_update.replace(':classroom_code', classroomCode);
        classroomModalForm.method = 'put';
        await loadClassroomData(classroomCode, classroomModalForm, classroomModalElement);
    }

    // open modal
    cleanupModalBackdrop();
    classroomModal.show();
}

/**
 *
 * @param {String|Number} classroomCode
 * @param {HTMLFormElement} form
 * @param {HTMLElement} modal
 * @returns
 */
async function loadClassroomData(classroomCode, form, modal) {
    // get data from server
    const response = await load_data(ROUTES.classrooms_show.replace(':classroom_code', classroomCode), CLASSROOM_NEW_MODAL);
    const classroom = response?.classroom || response?.data?.classroom || {};
    if (!classroom) {
        console.log('Error Loading Data for student view modal');
        return false;
    }
    // classroom data fill
    for (const [key, value] of Object.entries(classroom)) {
        modal.querySelectorAll(`[name="${key}"], #${key}`).forEach(field => {
            setFieldValue(field, value);
        });
    }

    return response;
}

/**
 * open view modal and assign data to its fields
 * @param {string} classroomCode
 */
async function openClassroomViewModal(classroomCode) {
    const ModalElement = document.querySelector(CLASSROOM_VIEW_MODAL);
    const viewModal = createBootstrapModal(ModalElement);

    if (!ModalElement || !viewModal) {
        console.error('Classroom view modal elements not found');
        return null;
    }

    // load data
    const response = await load_data(ROUTES.classrooms_show.replace(':classroom_code', classroomCode), CLASSROOM_VIEW_MODAL);

    const classroom = response?.classroom || response?.data?.classroom || {};
    const students = response?.students || response?.data?.students || {};
    if (!classroom) {
        console.log('Error Loading Data for classroom view modal');
        return false;
    }
    // classroom data fill
    for (const [key, value] of Object.entries(classroom)) {
        ModalElement.querySelectorAll(`[name="${key}"], #${key}`).forEach(field => {
            setFieldValue(field, value);
        });
    }

    // render students list data
    const studentsListRender = document.querySelector(CLASSROOM_VIEW_MODAL_STUDENTS_LIST);
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
    const table = new DataTable(CLASSROOM_DT_INDEX_TABLE, {
        processing: true,
        serverSide: true,
        ajax: {
            url: ROUTES.classrooms_dt_index,
            data: (d) => {
                d.showDeleted = dt_show_deleted_records; // custom param
            }
        },
        columns: [
            { data: null, defaultContent: '', className: 'select-checkbox', orderable: false },
            { data: 'classroom__name', name: 'classes.name', orderable: true, searchable: true },
            { data: 'classroom__class_code', name: 'classes.class_code', orderable: true, searchable: true },
            { data: 'classroom__grade', name: 'classes.grade', orderable: true, searchable: true },
        ],
        layout: {
            topStart: {
                buttons: [
                    { text: 'New', className: 'btn-primary', action: () => setupClassroomModal('new') },
                    {
                        text: 'View', className: 'btn-info dt-action-btn', action: () => {
                            const selected = table.rows({ selected: true }).data();
                            if (selected.length) openClassroomViewModal(selected[0].classroom__class_code);
                        }
                    },
                    {
                        text: 'Edit', className: 'btn-info dt-action-btn', action: () => {
                            const selected = table.rows({ selected: true }).data();
                            if (selected.length) setupClassroomModal('edit', selected[0].classroom__class_code);
                        }
                    },
                    {
                        text: 'Attach/Detach Students', className: 'btn-primary dt-action-btn', action: () => {
                            const selected = table.rows({ selected: true }).data();
                            if (selected.length) openClassroomStudentsModal(selected[0].classroom__class_code);
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
 * submit delete form classrooms
 * @param {object} row - row data
 * @param {Object} table - data table
 */
function handleDeleteRestore(row, table) {
    const actionText = row.classroom__is_deleted ? "Restore" : "Delete";
    const message = `Confirm ${actionText} classroom: ${row.classroom__name}?`;
    const action = ROUTES.classrooms_destroy.replace(':classroom_code', row.classroom__class_code);
    DT_handleDeleteRestore(table, action, message)
}
