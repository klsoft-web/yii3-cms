document.getElementById('name')?.addEventListener('change', (event) => {
    const text = event.target.value.trim();
    if (text.length > 0) {
        const slugEl = document.getElementById('slug');
        const slugText = slugEl.value.trim();
        if (slugText.length === 0) {
            const csrf = getCsrf();
            if (csrf !== null) {
                slugEl.readOnly = true;
                const request = new XMLHttpRequest();
                request.open('POST', '/admin/slug');
                request.onload = function () {
                    slugEl.readOnly = false;
                    if (request.status === 200) {
                        slugEl.value = request.responseText;
                    }
                };
                request.onerror = function () {
                    slugEl.readOnly = false;
                };

                const formData = new FormData();
                formData.append('_csrf', csrf);
                formData.append('text', text);
                request.send(formData);
            }
        }
    }
});

function getCsrf() {
    const forms = document.getElementsByTagName('form');
    if (forms.length > 0) {
        const form = forms[0];
        for (const child of form.children) {
            if (child.hasAttribute('name') &&
                child.attributes['name'].value === '_csrf') {
                return child.value;
            }
        }
    }
    return null;
}
