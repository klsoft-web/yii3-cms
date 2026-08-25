setOrderThenAddEventListenerToNavItems(document.getElementById('nav-items-container'));

document.getElementById('add-nav-item-type')?.addEventListener('change', (event) => {
    if (event.target.value === 'Url') {
        document.getElementById('add-nav-item-search-container').classList.add('d-none');
        document.getElementById('add-nav-item-action-name-container').classList.remove('d-none');
        document.getElementById('add-nav-item-btn').disabled = false;
    } else {
        document.getElementById('add-nav-item-action-name-container').classList.add('d-none');
        document.getElementById('add-nav-item-search-container').classList.remove('d-none');
        document.getElementById('add-nav-item-btn').disabled = true;
        document.getElementById('add-nav-item-search-result').innerHTML = '';
        const searchEl = document.getElementById('add-nav-item-search-entity');
        if (searchEl !== null) {
            findEntities(searchEl.value);
        }
    }
});

document.getElementById('add-nav-item-search-entity')?.addEventListener('input', (event) => {
    findEntities(event.target.value);
});

function findEntities(searchText) {
    const searchResultEl = document.getElementById('add-nav-item-search-result');
    if (searchResultEl !== null) {
        const searchTextTrimmed = searchText.trim();
        if (searchTextTrimmed.length > 0) {
            const csrf = getCsrf();
            if (csrf !== null) {
                searchResultEl.innerHTML = ' <span id="add-nav-item-spinner" class="spinner-border text-secondary" aria-hidden="true"/>';
                const request = new XMLHttpRequest();
                request.open('POST', '/admin/nav-find-entities');
                request.onload = function () {
                    if (request.status === 200) {
                        searchResultEl.innerHTML = request.responseText;
                        addEventListenerToSearchResult();
                    } else {
                        searchResultEl.innerHTML = '';
                    }
                };
                request.onerror = function () {
                    searchResultEl.innerHTML = '';
                };

                const formData = new FormData();
                formData.append('_csrf', csrf);
                formData.append('search', searchTextTrimmed);
                formData.append('entity_type', document.getElementById('add-nav-item-type')?.value);
                request.send(formData);
            }
        } else {
            searchResultEl.innerHTML = '';
        }
    }
}

function getCsrf() {
    const forms = document.getElementsByTagName('form');
    if (forms.length > 0) {
        const form = forms[0];
        for (const child of form.children) {
            if (child.hasAttribute('name') && child.attributes['name'].value === '_csrf') {
                return child.value;
            }
        }
    }
    return null;
}

addEventListenerToSearchResult();

function addEventListenerToSearchResult() {
    const inputElList = document.getElementById('add-nav-item-search-result')?.getElementsByTagName('input')
    if (inputElList !== null) {
        for (const inputEl of inputElList) {
            inputEl.addEventListener('click', () => {
                let isAllUnchecked = true;
                for (const inputEl of inputElList) {
                    if (inputEl.checked) {
                        isAllUnchecked = false;
                        break;
                    }
                }
                document.getElementById('add-nav-item-btn').disabled = isAllUnchecked;
            });
        }
    }
}

document.getElementById('add-nav-item-btn')?.addEventListener('click', () => {
    const navItems = [];
    if (document.getElementById('add-nav-item-type')?.value === 'Url') {
        if (validateUrlNavItemName('nav-item-name') &
            validateUrlNavItemValue('nav-item-value')) {
            navItems.push({
                name: document.getElementById('nav-item-name').value,
                value: document.getElementById('nav-item-value').value
            });
        }
    } else {
        const inputElList = document.getElementById('add-nav-item-search-result')?.getElementsByTagName('input')
        if (inputElList !== null) {
            for (const inputEl of inputElList) {
                if (inputEl.checked) {
                    navItems.push({
                        name: inputEl.nextElementSibling.innerText, value: inputEl.value
                    });
                }
            }
        }
    }
    if (navItems.length > 0) {
        fetchNavItems(navItems);
    }
});

function validateUrlNavItemName(elId) {
    let isValid = false;
    const navItemNameEl = document.getElementById(elId);
    if (navItemNameEl !== null) {
        navItemNameEl.classList.remove('is-invalid');

        const navItemNameRegExp = /^[^\s+](.)*/;
        isValid = navItemNameRegExp.test(navItemNameEl.value)
        if (!isValid) {
            navItemNameEl.classList.add('is-invalid');
        }
    }

    return isValid;
}

function validateUrlNavItemValue(elId) {
    let isValid = false;
    const navItemValueEl = document.getElementById(elId);
    if (navItemValueEl !== null) {
        navItemValueEl.classList.remove('is-invalid');
        const navItemValueRegExp = navItemValueEl.value.startsWith('/') ? /^\/[a-z0-9]+\/?([a-z0-9-]\/?)*$/ : /^(https?:\/\/)?([\w-]+\.)+[\w-]+(\/[\w-]*)*$/;
        isValid = navItemValueRegExp.test(navItemValueEl.value);
        if (!isValid) {
            navItemValueEl.classList.add('is-invalid');
        }
    }

    return isValid;
}

function fetchNavItems(navItems) {
    const csrf = getCsrf();
    if (csrf !== null) {
        showAddNavItemBtnSpinner();

        const request = new XMLHttpRequest();
        request.open('POST', '/admin/nav-fetch-nav-items');
        request.onload = function () {
            if (request.status === 200) {
                const navItemsContainer = document.getElementById('nav-items-container');
                if (navItemsContainer != null) {
                    navItemsContainer.innerHTML += request.responseText;
                    setOrderThenAddEventListenerToNavItems(navItemsContainer);
                }
            }
            hideAddNavItemBtnBtnSpinner();
        };
        request.onerror = function () {
            hideAddNavItemBtnBtnSpinner();
        };

        const formData = new FormData();
        formData.append('_csrf', csrf);
        formData.append('nav_item_type', document.getElementById('add-nav-item-type').value);
        for (let i = 0; i < navItems.length; i++) {
            formData.append('nav_items[' + i + '][name]', navItems[i].name);
            formData.append('nav_items[' + i + '][value]', navItems[i].value);
        }
        request.send(formData);
    }
}

function showAddNavItemBtnSpinner() {
    document.getElementById('add-nav-item-btn').disabled = true;
    document.getElementById('add-nav-item-btn-spinner').classList.remove('d-none');
}

function hideAddNavItemBtnBtnSpinner() {
    document.getElementById('add-nav-item-btn-spinner').classList.add('d-none');
    document.getElementById('add-nav-item-btn').disabled = false;
}

function setOrderThenAddEventListenerToNavItems(navItemsContainer) {
    if (navItemsContainer === null) {
        return;
    }

    const navItemElList = navItemsContainer.getElementsByClassName('nav-item');
    for (let i = 0; i < navItemElList.length; i++) {
        const navItemEl = navItemElList[i];
        const navItemOrderElName = 'nav_items[' + navItemEl.attributes['data-key'].value + '][order]';
        for (const inputEl of navItemEl.getElementsByTagName('input')) {
            if (inputEl.name === navItemOrderElName) {
                inputEl.value = i + 1;
                break;
            }
        }

        const navItemUpElList = navItemEl.getElementsByClassName('nav-item-up');
        if (navItemUpElList.length > 0) {
            if (i > 0) {
                navItemUpElList[0].addEventListener('click', () => {
                    onNavItemUpClicked(i, navItemElList);
                });
            }
            navItemUpElList[0].disabled = i === 0;
        }
        const navItemEditElList = navItemEl.getElementsByClassName('nav-item-edit');
        if (navItemEditElList.length > 0) {
            navItemEditElList[0].addEventListener('click', () => {
                onNavItemEditClicked(navItemElList[i]);
            });
        }
        const navItemRemoveElList = navItemEl.getElementsByClassName('nav-item-remove');
        if (navItemRemoveElList.length > 0) {
            navItemRemoveElList[0].addEventListener('click', () => {
                onNavItemRemoveClicked(i, navItemElList);
            });
        }
    }
}

function onNavItemUpClicked(index, navItemElList) {
    if (index === 0) {
        return;
    }

    const navItemsContainer = document.getElementById('nav-items-container');
    if (navItemsContainer != null) {
        let content = '';
        for (let i = 0; i < navItemElList.length; i++) {
            if (i !== index - 1) {
                if (i === index) {
                    content += navItemElList[i].outerHTML;
                    content += navItemElList[index - 1].outerHTML;
                } else {
                    content += navItemElList[i].outerHTML;
                }
            }
        }
        navItemsContainer.innerHTML = content;
        setOrderThenAddEventListenerToNavItems(navItemsContainer);
    }
}

function onNavItemEditClicked(navItemEl) {
    const navItemKey = navItemEl.attributes['data-key'].value;
    const navItemTypeElName = 'nav_items[' + navItemKey + '][nav_item_type]';
    let navItemType = '';
    const navItemNameElName = 'nav_items[' + navItemKey + '][name]';
    const navItemValueElName = 'nav_items[' + navItemKey + '][value]';
    const navItemKeyEl = document.getElementById('nav-item-key-edit-dialog');
    if (navItemKeyEl !== null) {
        navItemKeyEl.value = navItemKey;
    }
    for (const inputEl of navItemEl.getElementsByTagName('input')) {
        if (inputEl.name === navItemTypeElName) {
            navItemType = inputEl.value;
        } else if (inputEl.name === navItemNameElName) {
            const navItemNameEditDialogEl = document.getElementById('nav-item-name-edit-dialog');
            if (navItemNameEditDialogEl !== null) {
                navItemNameEditDialogEl.value = inputEl.value;
                navItemNameEditDialogEl.classList.remove('is-invalid');
            }

        } else if (inputEl.name === navItemValueElName) {
            const navItemValueEditDialogEl = document.getElementById('nav-item-value-edit-dialog');
            if (navItemValueEditDialogEl !== null) {
                navItemValueEditDialogEl.value = inputEl.value;
                navItemValueEditDialogEl.classList.remove('is-invalid');
            }
        }
    }
    const navItemValueContainer = document.getElementById('nav-item-value-edit-dialog-container');
    if (navItemValueContainer !== null) {
        if (navItemType === 'Url') {
            navItemValueContainer.classList.remove('d-none');
        } else {
            navItemValueContainer.classList.add('d-none');
        }
    }
    const navItemEditDialog = document.getElementById('nav-item-edit-dialog');
    navItemEditDialog.addEventListener('shown.bs.modal', () => {
        document.getElementById('nav-item-name-edit-dialog')?.focus();
    });
    (new bootstrap.Modal(navItemEditDialog, {backdrop: false})).show();
}

document.getElementById('nav-item-edit-dialog-apply-btn')?.addEventListener('click', () => {
    const navItemKeyEl = document.getElementById('nav-item-key-edit-dialog');
    if (navItemKeyEl !== null) {
        const navItemKey = navItemKeyEl.value;
        const navItemsContainer = document.getElementById('nav-items-container');
        if (navItemsContainer != null) {
            for (const navItemEl of navItemsContainer.getElementsByClassName('nav-item')) {
                if (navItemEl.attributes['data-key'].value === navItemKey) {
                    const navItemTypeElName = 'nav_items[' + navItemKey + '][nav_item_type]';
                    let navItemType = '';
                    const navItemNameElName = 'nav_items[' + navItemKey + '][name]';
                    let navItemNameEl = null;
                    const navItemValueElName = 'nav_items[' + navItemKey + '][value]';
                    let navItemValueEl = null;
                    for (const inputEl of navItemEl.getElementsByTagName('input')) {
                        if (inputEl.name === navItemTypeElName) {
                            navItemType = inputEl.value;
                        } else if (inputEl.name === navItemNameElName) {
                            navItemNameEl = inputEl;
                        } else if (inputEl.name === navItemValueElName) {
                            navItemValueEl = inputEl;
                        }
                    }

                    let isValid = validateUrlNavItemName('nav-item-name-edit-dialog');
                    if (navItemType === 'Url') {
                        isValid = validateUrlNavItemValue('nav-item-value-edit-dialog') && isValid;
                    }

                    if (isValid) {
                        if (navItemNameEl !== null) {
                            const navItemName = document.getElementById('nav-item-name-edit-dialog')?.value;
                            navItemNameEl.value = navItemName;
                            const navItemTextElList = navItemEl.getElementsByClassName('nav-item-text');
                            if (navItemTextElList.length > 0) {
                                navItemTextElList[0].innerText = navItemName;
                            }
                        }
                        if (navItemType === 'Url' &&
                            navItemValueEl != null) {
                            navItemValueEl.value = document.getElementById('nav-item-value-edit-dialog')?.value;
                        }
                        bootstrap.Modal.getInstance(document.getElementById('nav-item-edit-dialog')).hide();
                    }
                }
            }
        }
    }
});

function onNavItemRemoveClicked(index, navItemElList) {
    const navItemsContainer = document.getElementById('nav-items-container');
    if (navItemsContainer != null) {
        let content = '';
        for (let i = 0; i < navItemElList.length; i++) {
            if (i !== index) {
                content += navItemElList[i].outerHTML;
            }
        }
        navItemsContainer.innerHTML = content;
        setOrderThenAddEventListenerToNavItems(navItemsContainer);
    }
}
