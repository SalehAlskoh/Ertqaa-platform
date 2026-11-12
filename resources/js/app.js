import './bootstrap';

document.querySelectorAll('[data-menu-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const menu = document.getElementById(button.getAttribute('aria-controls'));
        if (!menu) return;
        const expanded = button.getAttribute('aria-expanded') === 'true';
        button.setAttribute('aria-expanded', String(!expanded));
        menu.classList.toggle('is-open', !expanded);
    });
});

document.querySelectorAll('[data-account-mode]').forEach((select) => {
    const form = select.closest('form');
    const update = () => {
        form.querySelectorAll('[data-account-pane]').forEach((pane) => {
            const enabled = pane.dataset.accountPane === select.value;
            pane.hidden = !enabled;
            pane.querySelectorAll('input, select, textarea').forEach((field) => {
                field.disabled = !enabled;
            });
        });
    };
    select.addEventListener('change', update);
    update();
});

document.querySelectorAll('form').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
            event.preventDefault();
            return;
        }
        if (form.method.toLowerCase() === 'post') {
            if (form.classList.contains('is-submitting')) {
                event.preventDefault();
                return;
            }
            form.classList.add('is-submitting');
            form.setAttribute('aria-busy', 'true');
        }
    });
});

window.addEventListener('pageshow', () => {
    document.querySelectorAll('form.is-submitting').forEach((form) => {
        form.classList.remove('is-submitting');
        form.removeAttribute('aria-busy');
    });
});
