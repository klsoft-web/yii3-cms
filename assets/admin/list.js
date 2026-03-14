document.getElementById('add-entity-btn')?.addEventListener('click', (event) => {
    edit(event.target.parentElement.attributes['data-edit-url'].value);
});

function edit(url) {
    location.assign(url);
}

for (const trEl of document.getElementsByClassName('entity-item-editable')) {
    trEl.addEventListener('click', (event) => {
        if (event.target.parentElement.hasAttribute('data-edit-url') &&
            event.target.getElementsByTagName('input').length === 0) {
            edit(trEl.attributes['data-edit-url'].value);
        }
    });
}

document.getElementById('delete-entities-btn')?.addEventListener('click', () => {
    if (getSelectedEntitiesIds().length > 0) {
        hideAllDeleteEntitiesAlert();
        (new bootstrap.Modal(document.getElementById('delete-entities-confirm-dialog'))).show();
    }
});

document.getElementById('delete-entities-confirm-btn')?.addEventListener('click', () => {
    const deleteIds = getSelectedEntitiesIds();
    if (deleteIds.length > 0) {
        hideAllDeleteEntitiesAlert();
        showDeleteEntitiesConfirmBtnSpinner();
        const deleteEntitiesBtn = document.getElementById('delete-entities-btn');
        const deleteUrl = deleteEntitiesBtn.attributes['data-delete-url'].value;
        const csrf = deleteEntitiesBtn.attributes['data-csrf'].value;
        const request = new XMLHttpRequest();
        request.open('POST', deleteUrl);
        request.onload = function () {
            hideDeleteEntitiesConfirmBtnSpinner();
            uncheckAll();
            if (request.status === 200) {
                location.reload();
            } else {
                if (request.status === 403) {
                    document.getElementById('delete-entities-forbidden-alert').classList.remove('d-none');
                } else {
                    document.getElementById('delete-entities-error-alert').classList.remove('d-none');
                }
            }
        };
        request.onerror = function () {
            hideDeleteEntitiesConfirmBtnSpinner();
            uncheckAll();
            document.getElementById('delete-entities-error-alert"').classList.remove('d-none');
        };

        const formData = new FormData();
        formData.append('_csrf', csrf);
        deleteIds.forEach((id) => {
            formData.append('delete_ids[]', id);
        })
        request.send(formData);
    }
});

function getSelectedEntitiesIds() {
    const deleteIds = [];
    for (const input of document.getElementById('entities-table').getElementsByClassName('entity-checkbox-selector')) {
        if (input.checked) {
            deleteIds.push(input.value);
        }
    }
    return deleteIds;
}

function uncheckAll() {
    const inputs = document.getElementById('entities-checkbox-header')?.getElementsByTagName('input');
    if (inputs !== null &&
        inputs.length > 0) {
        inputs[0].checked = false;
    }
    const entityCheckboxList = document.getElementById('entities-table')?.getElementsByClassName('entity-checkbox-selector');
    if (entityCheckboxList !== null) {
        for (const inputEl of entityCheckboxList) {
            inputEl.checked = false;
        }
    }
}

function showDeleteEntitiesConfirmBtnSpinner() {
    document.getElementById('delete-entities-confirm-btn').disabled = true;
    document.getElementById('delete-entities-confirm-btn-spinner').classList.remove('d-none');
}

function hideDeleteEntitiesConfirmBtnSpinner() {
    document.getElementById('delete-entities-confirm-btn-spinner').classList.add('d-none');
    document.getElementById('delete-entities-confirm-btn').disabled = false;
}

function hideAllDeleteEntitiesAlert() {
    for (const alert of document.getElementsByClassName('alert')) {
        alert.classList.add('d-none');
    }
}

const inputElList = document.getElementById('entities-checkbox-header')?.getElementsByTagName('input');
if (inputElList != null &&
    inputElList.length > 0) {
    inputElList[0].addEventListener('click', (event) => {
        const isEntitiesCheckboxChecked = event.target.checked
        const entitiesTable = document.getElementById('entities-table');
        const entityCheckboxList = entitiesTable.getElementsByClassName('entity-checkbox-selector');
        for (const inputEl of entityCheckboxList) {
            inputEl.checked = isEntitiesCheckboxChecked;
        }
    });
}

const entityCheckboxList = document.getElementById('entities-table')?.getElementsByClassName('entity-checkbox-selector');
if (entityCheckboxList !== null) {
    for (const inputEl of entityCheckboxList) {
        inputEl.addEventListener('click', (event) => {
            const inputs = document.getElementById('entities-checkbox-header')?.getElementsByTagName('input');
            if (inputs !== null &&
                inputs.length > 0) {
                const allEntitiesCheckbox = inputs[0];
                if (event.target.checked) {
                    allEntitiesCheckbox.checked = true;
                } else {
                    let isAllUnchecked = true;
                    for (const inputEl of entityCheckboxList) {
                        if (inputEl.checked) {
                            isAllUnchecked = false;
                            break;
                        }
                    }
                    if (isAllUnchecked) {
                        allEntitiesCheckbox.checked = false;
                    }
                }
            }
        });
    }
}
