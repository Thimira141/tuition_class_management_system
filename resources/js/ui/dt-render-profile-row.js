/**
 * Build a row with profile image and text fields for data table
 * @param {string} imageSrc
 * @param {string[]} texts
 * @returns {HTMLDivElement}
 */
export function renderProfileRow(imageSrc, texts) {
    const row = document.createElement('div');
    row.className = 'row';

    const colImage = document.createElement('div');
    colImage.className = 'col-auto';

    const img = document.createElement('img');
    img.className = 'p-1 mx-2 rounded-circle bg-body-secondary';
    img.style = 'height: 50px; width: 50px';
    img.alt = 'Profile';

    const safeImageSrc = imageSrc && String(imageSrc).trim() ? imageSrc : DEFAULT_ROUTES['MEMBER-IMG-PLACEHOLDER'];
    img.src = safeImageSrc;
    img.onerror = () => { img.src = DEFAULT_ROUTES['MEMBER-IMG-PLACEHOLDER']; };
    colImage.appendChild(img);

    const colText = document.createElement('div');
    colText.className = 'col';

    texts.forEach(text => {
        const p = document.createElement('p');
        p.className = 'mb-1';
        p.innerText = text;
        colText.appendChild(p);
    });

    row.append(colImage, colText);
    return row;
}
