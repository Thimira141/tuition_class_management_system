import TomSelect from "tom-select";
import { clearFormErrors } from "./ajax";
import { load_data, setFieldValue, cleanupModalBackdrop, createBootstrapModal } from "./utility";
import { formEventsTracker as guardian_formEventsTracker, triggerNewGuardianModal} from "./guardians";

// identifiers for NEW STUDENT modal
const STUDENT_NEW_MODAL = '#new-student-model';
const STUDENT_NEW_MODAL_TRIGGER = '#new-student-add-model-init-btn';
const STUDENT_NEW_MODAL_FORM = '#new-edit-student-form';
const STUDENT_NEW_MODAL_GUARDIAN_SELECT = '#new-student-model #student__guardian_id';

// NEW GUARDIAN modal
const GUARDIAN_NEW_MODAL = '#new-guardian-model';
const GUARDIAN_NEW_MODAL_TRIGGER = '#new-guardian-add-model-init-btn';
const GUARDIAN_NEW_MODAL_FORM = '#new-edit-guardian-form';

// variables

// inits
document.addEventListener('DOMContentLoaded', () => {
    // init guardians select in new,edit form
    const studentGuardianSelect = initStudentGuardianSelect();
    // init new,edit model actions
    triggerNewStudentModal();
    triggerNewGuardianModal(); // for guardian modal

    formEventsTracker(studentGuardianSelect);

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

// handle guardian info add modal

// function toggleChildVisibilityByParentValue(_child, _wrapper, value) {
//     if (!_child || !_wrapper) return;

//     const targetValue = _child.getAttribute('data-show-on-parent-value');
//     const isMatch = Array.isArray(value) ? value.includes(targetValue) : value == targetValue;

//     if (isMatch) {
//         _wrapper.classList.remove('visually-hidden');
//     } else {
//         _wrapper.classList.add('visually-hidden');
//     }
// }
