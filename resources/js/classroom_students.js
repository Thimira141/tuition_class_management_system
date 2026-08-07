import { clearFormErrors, submitAjaxForm } from "./ajax";
import { showConfirmToast } from "./message_models";
import { cleanupModalBackdrop, createBootstrapModal, DT_setDeletedState, dt_show_deleted_records } from "./utility";
import { renderProfileRow } from './ui/dt-render-profile-row';

/**
 * DT imports
 */
import DataTable from 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';
import 'datatables.net-select-bs5';

// identifiers EDIT CLASSROOM_STUDENTS modal
const CLASSROOM_STUDENTS_EDIT_MODAL = '#new-classroom-students-model';

// data table
const CLASSROOM_STUDENTS_DT_INDEX_TABLE = '#dt-classroom-students-index-table'; // table

// variables
let StudentClassroomDTable;

// NOTE classroom__, student__ are prefix, classes,students are table name

// functions

/**
 * Opens the Classroom Students modal and initializes the DataTable.
 * Cleans up backdrop and attaches close event to destroy DT.
 *
 * @param {string} classroomCode - The classroom code used to fetch student records.
 * @returns {void}
 */
export function openClassroomStudentsModal(classroomCode) {
    const modalEl = document.querySelector(CLASSROOM_STUDENTS_EDIT_MODAL);
    const modal = createBootstrapModal(modalEl);

    if (!modalEl || !modal) {
        console.error('CLASSROOM_STUDENTS modal elements not found');
        return;
    }

    cleanupModalBackdrop();
    modal.show();

    // init DT
    StudentClassroomDTable = initIndexDataTable(classroomCode);
    DT_setDeletedState('active', StudentClassroomDTable);

    // destroy DT when modal closes
    modalEl.addEventListener('hidden.bs.modal', closeClassroomStudentsModalEvent, { once: true });
}

/**
 * Handles modal close event by destroying the DataTable instance.
 *
 * @returns {void}
 */
function closeClassroomStudentsModalEvent() {
    if (StudentClassroomDTable) {
        StudentClassroomDTable.destroy();
        StudentClassroomDTable = null;
    }
}

/**
 * Initializes the DataTable for classroom students.
 *
 * @param {string} classroomCode - The classroom code used to build AJAX route.
 * @returns {DataTable} - The initialized DataTable instance.
 */
function initIndexDataTable(classroomCode) {
    const table = new DataTable(CLASSROOM_STUDENTS_DT_INDEX_TABLE, {
        processing: true,
        serverSide: true,
        ajax: {
            url: ROUTES.classroom_students_dt_index.replace(':classroom', classroomCode),
            data: d => { d.studentState = dt_show_deleted_records === 'active' ? 'attached' : 'detached'; }
        },
        columns: [
            { data: null, defaultContent: '', className: 'select-checkbox', orderable: false },
            {
                data: 'student__name',
                name: 'students.name',
                render: (_, __, row) => renderProfileRow(
                    row.student__cover_img_url,
                    [row.student__name, '#' + row.student__student_code]
                )
            },
            { data: 'student__dob', name: 'students.dob' },
            { data: 'student__id', visible: false },
        ],
        layout: {
            topStart: {
                buttons: [
                    {
                        text: 'Attach',
                        className: 'btn-primary dt-action-btn',
                        action: () => {
                            const ids = table.rows({ selected: true }).data().pluck('student__id').toArray();
                            if (ids.length) handleAttachDetachRecords(classroomCode, ids, "Attach").then(() => table.ajax.reload(null, false));
                        }
                    },
                    {
                        text: 'Detach',
                        className: 'btn-warning dt-action-btn',
                        action: () => {
                            const ids = table.rows({ selected: true }).data().pluck('student__id').toArray();
                            if (ids.length) handleAttachDetachRecords(classroomCode, ids, "Detach").then(() => table.ajax.reload(null, false));
                        }
                    },
                    {
                        extend: 'collection',
                        text: 'Records Filter',
                        buttons: [
                            { text: 'Attached Students', className: 'buttons-active', action: () => DT_setDeletedState('active', table) },
                            { text: 'Detached Students', className: 'buttons-deleted', action: () => DT_setDeletedState('deleted', table) }
                        ]
                    }
                ]
            }
        },
        select: { style: 'multiple', selector: 'td.select-checkbox' },
        order: [[1, 'asc']]
    });

    return table;
}

/**
 * Handles attaching or detaching students from a classroom.
 * Builds a hidden form and submits via AJAX.
 *
 * @param {string} classroomCode - The classroom code used in route.
 * @param {Array<number>} studentIds - Array of student IDs to attach/detach.
 * @param {string} method - Either "Attach" or "Detach".
 * @returns {Promise<void>} - Resolves when AJAX succeeds.
 */
function handleAttachDetachRecords(classroomCode, studentIds, method) {
    return new Promise(resolve => {
        const action = method === 'Attach'
            ? ROUTES.classroom_student_attach.replace(':classroom', classroomCode)
            : ROUTES.classroom_student_detach.replace(':classroom', classroomCode);

        const message = method === 'Attach'
            ? "Confirm Student(s) Attach!"
            : "Confirm Student(s) Detach!";

        showConfirmToast(message, () => {
            const form = document.createElement('form');
            form.action = action;
            form.method = 'post';

            studentIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'student_ids[]';
                input.value = id;
                form.appendChild(input);
            });

            form.addEventListener('ajax:success', () => resolve(), { once: true });

            submitAjaxForm(form);
        });
    });
}
