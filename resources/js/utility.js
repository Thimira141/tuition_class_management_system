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

/**
 * previewing an selected(uploaded) image
 * @param {HTMLFormElement} element
 */
export function previewSelectedImage(element) {
    if (element.files && element.files[0]) {
        const imgUrl = URL.createObjectURL(element.files[0]);
        const targetImg = document.querySelector(element.dataset.targetImg);
        if (targetImg) {
            targetImg.src = imgUrl;
        }
    }
}
