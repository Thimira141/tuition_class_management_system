// import and setup bootstrap
import './bootstrap';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// import and setup theme toggle button
import initThemeToggle from './theme';
initThemeToggle();

// import and setup custom message modals
import { showModal, showConfirmModal } from './message_models';
window.showModal = showModal;
window.showConfirmModal = showConfirmModal;

// setup ajax forms init
import initAjaxForms from './ajax';
initAjaxForms();

// setting debug mode
window.debug = (...args) => {
    if (import.meta.env.MODE !== 'production') {
        console.debug(...args);
    }
};

