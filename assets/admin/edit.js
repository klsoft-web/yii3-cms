document.getElementById('cancel-btn')?.addEventListener('click', (event) => {
    location.assign(event.target.attributes['data-cancel-url'].value);
});

if (document.getElementById('content') !== null) {
    CKEDITOR.replace('content', {
        filebrowserBrowseUrl: '/admin/file-browser/browser/file',
        filebrowserImageBrowseUrl: '/admin/file-browser/browser/image'
    });
}

document.getElementById('summary-img-path-icon')?.addEventListener('click', (event) => {
    openFileBrowser();
});

document.getElementById('edit-summary-img-btn')?.addEventListener('click', (event) => {
    openFileBrowser();
});

function openFileBrowser()
{
    const height = window.innerHeight * 3 / 4;
    const width = window.innerWidth * 3 / 4;
    window.open('/admin/file-browser/browser/image?target_ids=summary-img-path-value,summary-img-path-icon,summary-img-path-img', '', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=' + width + ',height=' + height);
}

document.getElementById('remove-summary-img-btn')?.addEventListener('click', (event) => {
    const summaryImgPathValueEl = document.getElementById('summary-img-path-value');
    if (summaryImgPathValueEl !== null) {
        summaryImgPathValueEl.value = null;
    }
    const summaryIconPathImgEl = document.getElementById('summary-img-path-icon');
    if (summaryIconPathImgEl !== null) {
        summaryIconPathImgEl.classList.remove('d-none');
    }
    const summaryImgPathImgEl = document.getElementById('summary-img-path-img');
    if (summaryImgPathImgEl !== null) {
        summaryImgPathImgEl.src = null;
    }
});
