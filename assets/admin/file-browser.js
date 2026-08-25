addEventListenerToFileItems();

function addEventListenerToFileItems() {
    const fileItemElList = document.getElementsByClassName('file-item');
    for (const fileItemEl of fileItemElList) {
        fileItemEl.addEventListener('click', () => {
            const inputElList = fileItemEl.getElementsByClassName('url')
            if (inputElList !== null &&
                inputElList.length > 0) {
                if (inputElList[0].classList.contains('directory')) {
                    fetchFiles(inputElList[0].value);
                } else {
                    onFileClicked(inputElList[0].value)
                }
            }
        });
    }
}

function onFileClicked(url) {
    const queryStringItems = getQueryStringItems();
    if (queryStringItems.CKEditorFuncNum) {
        opener.window.CKEDITOR.tools.callFunction(queryStringItems.CKEditorFuncNum, url);
    } else if (queryStringItems.target_ids) {
        queryStringItems.target_ids.split(',').forEach((elId) => {
            const el = opener.document.getElementById(elId);
            if (el !== null) {
                if (el.tagName === 'INPUT') {
                    el.value = url;
                } else if (el.tagName === 'IMG') {
                    el.src = url;
                } else if (el.tagName === 'I') {
                    el.classList.add('d-none');
                }
            }
        });
    }
    window.close();
}

function getQueryStringItems() {
    let queryStringItems = {}, nameValue = null;
    const nameValueItems = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for (let i = 0; i < nameValueItems.length; i++) {
        nameValue = nameValueItems[i].split('=');
        queryStringItems[nameValue[0]] = nameValue[1];
    }
    return queryStringItems;
}

addEventListenerToBreadcrumb();

function addEventListenerToBreadcrumb() {
    const spanElList = document.getElementById('breadcrumb')?.getElementsByTagName('span')
    if (spanElList !== null) {
        for (const spanEl of spanElList) {
            spanEl.addEventListener('click', () => {
                fetchFiles(spanEl.attributes['data-directory'].value);
            });
        }
    }
}

function fetchFiles(directory) {
    const csrf = document.getElementById('csrf').value;
    const request = new XMLHttpRequest();
    request.open('POST', document.getElementById('files-url').value);
    request.onload = function () {
        if (request.status === 200) {
            const contentEl = document.getElementById('content');
            if (contentEl !== null) {
                contentEl.innerHTML = request.responseText;
                addEventListenerToFileItems();
            }
            const currentDirEl = document.getElementById('current-dir');
            if (currentDirEl !== null) {
                currentDirEl.value = directory;
            }
            setBreadcrumb();
            addEventListenerToBreadcrumb();
        }
    };

    const formData = new FormData();
    formData.append('_csrf', csrf);
    formData.append('directory', directory);
    request.send(formData);
}


document.getElementById('upload-btn')?.addEventListener('click', () => {
    document.getElementById('file')?.click();
});

document.getElementById('file')?.addEventListener('change', (event) => {
    const csrf = document.getElementById('csrf').value;
    const request = new XMLHttpRequest();
    request.open('POST', document.getElementById('upload-url').value);
    request.onload = function () {
        if (request.status === 200) {
            onFileClicked(request.responseText);
        } else if (request.responseText !== '') {
            alert(request.responseText);
        }
    };

    const formData = new FormData();
    formData.append('_csrf', csrf);
    formData.append('directory', document.getElementById('current-dir').value);
    formData.append('upload', event.target.files[0]);
    request.send(formData);
});

document.getElementById('show-create-folder-dialog-btn')?.addEventListener('click', () => {
    const createFolderDialog = document.getElementById('create-folder-dialog');
    createFolderDialog.addEventListener('shown.bs.modal', () => {
        document.getElementById('folder-name')?.focus();
    });
    (new bootstrap.Modal(createFolderDialog)).show();
});

document.getElementById('create-folder-btn')?.addEventListener('click', () => {
    const folderNameEl = document.getElementById('folder-name');
    if (validateFolderName(folderNameEl)) {
        showCreateFolderBtnSpinner();
        const folderName = folderNameEl.value.trim();
        const csrf = document.getElementById('csrf').value;
        const createFolderDialog = bootstrap.Modal.getInstance(document.getElementById('create-folder-dialog'));
        const request = new XMLHttpRequest();
        request.open('POST', document.getElementById('create-folder-url').value);
        request.onload = function () {
            hideCreateFolderBtnSpinner();
            if (request.status === 200) {
                createFolderDialog.hide();
                const contentEl = document.getElementById('content');
                if (contentEl !== null) {
                    contentEl.innerHTML = '';
                }
                const currentDirEl = document.getElementById('current-dir');
                if (currentDirEl !== null) {
                    currentDirEl.value = currentDirEl.value + folderName + '/';
                }
                setBreadcrumb();
                addEventListenerToBreadcrumb();
            } else {
                folderNameEl.classList.add('is-invalid');
                document.getElementById('folder-name-error').classList.remove('d-none');
            }
        };
        request.onerror = function () {
            hideCreateFolderBtnSpinner();
        };

        const formData = new FormData();
        formData.append('_csrf', csrf);
        formData.append('directory', document.getElementById('current-dir').value);
        formData.append('folder_name', folderName);
        request.send(formData);
    }
});

function validateFolderName(folderNameEl) {
    folderNameEl.classList.remove('is-invalid');

    const folderName = folderNameEl.value.trim();
    const regExp = /^[a-z0-9]+[a-z0-9_-]*[a-z0-9]+$/;
    const isValid = regExp.test(folderName);
    if (!isValid) {
        folderNameEl.classList.add('is-invalid');
    }
    return isValid;
}

function showCreateFolderBtnSpinner() {
    document.getElementById('create-folder-btn').disabled = true;
    document.getElementById('create-folder-btn-spinner').classList.remove('d-none');
}

function hideCreateFolderBtnSpinner() {
    document.getElementById('create-folder-btn-spinner').classList.add('d-none');
    document.getElementById('create-folder-btn').disabled = false;
}

function setBreadcrumb() {
    const breadcrumbEl = document.getElementById('breadcrumb');
    if (breadcrumbEl !== null) {
        const directory = document.getElementById('current-dir').value;
        const uploadDir = document.getElementById('upload-dir').value;
        if (directory === uploadDir) {
            breadcrumbEl.classList.add('d-none');
        } else {
            const breadcrumbItems = [];
            const uploadDirItems = uploadDir.split('/');
            let dirName = "";
            let item = null;
            for (let i = 0, length = uploadDirItems.length; i < length; i++) {
                item = uploadDirItems[i];
                if (item) {
                    dirName = item;
                }
            }
            breadcrumbItems.push({directory: uploadDir, name: dirName});
            const directoryItems = directory.substring(uploadDir.length).split('/');
            for (let i = 0, length = directoryItems.length; i < length; i++) {
                item = directoryItems[i];
                if (item) {
                    breadcrumbItems.push({directory: uploadDir + "/" + item + "/", name: item});
                }
            }
            let content = "";
            const breadcrumbItemsLength = breadcrumbItems.length;
            for (let i = 0; i < breadcrumbItemsLength - 1; i++) {
                item = breadcrumbItems[i];
                content = content + '<li class="breadcrumb-item"><span class="btn-link" data-directory="' + item.directory + '">' + item.name + '</span></li>';
            }
            item = breadcrumbItems[breadcrumbItemsLength - 1];
            content = content + '<li class="breadcrumb-item active">' + item.name + '</li>';
            breadcrumbEl.innerHTML = content;
            breadcrumbEl.classList.remove('d-none');
        }
    }
}
