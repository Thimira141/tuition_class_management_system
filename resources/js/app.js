// import and setup bootstrap
import './bootstrap';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// import and setup utilities
import * as utility from './utility';
window.utility = utility;
// activate logout form function
utility.submitLogoutForm()

// import and setup theme toggle button
import initThemeToggle from './theme';
initThemeToggle();

// import and setup custom message modals
import { showToast, showConfirmModal } from './message_models';
window.showToast = showToast;
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

