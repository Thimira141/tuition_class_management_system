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
