import './bootstrap';

document.querySelectorAll('[data-menu-toggle]').forEach((button) => {
    const menu = document.getElementById(button.getAttribute('aria-controls'));
    if (!menu) return;
    button.addEventListener('click', () => {
        const expanded = button.getAttribute('aria-expanded') === 'true';
        button.setAttribute('aria-expanded', String(!expanded));
        menu.classList.toggle('is-open', !expanded);
    });
    const closeMenu = (returnFocus = false) => {
        button.setAttribute('aria-expanded', 'false');
        menu.classList.remove('is-open');
        if (returnFocus) button.focus();
    };
    const closeOnEscape = (event) => {
        if (event.key === 'Escape' && button.getAttribute('aria-expanded') === 'true') {
            event.preventDefault();
            closeMenu(true);
        }
    };
    menu.addEventListener('keydown', closeOnEscape);
    button.addEventListener('keydown', closeOnEscape);
    document.addEventListener('click', (event) => {
        if (button.getAttribute('aria-expanded') === 'true' && !menu.contains(event.target) && !button.contains(event.target)) closeMenu();
    });
    window.matchMedia('(min-width: 768px)').addEventListener('change', () => closeMenu());
});

const drawer = document.querySelector('[data-workspace-drawer]');
if (drawer) {
    const openButtons = [...document.querySelectorAll('[data-drawer-open]')];
    const backdrop = document.querySelector('[data-drawer-backdrop]');
    const background = document.querySelector('[data-drawer-background]');
    const desktop = window.matchMedia('(min-width: 1280px)');
    let opener = null;
    let open = false;
    const focusable = () => [...drawer.querySelectorAll('a[href], button:not(:disabled), input:not(:disabled), [tabindex="0"]')].filter((node) => node.getClientRects().length);
    const closeDrawer = (returnFocus = true) => {
        open = false;
        drawer.classList.remove('is-open');
        drawer.removeAttribute('aria-modal');
        drawer.removeAttribute('role');
        drawer.inert = !desktop.matches;
        drawer.hidden = !desktop.matches;
        backdrop.hidden = true;
        background.inert = false;
        document.body.style.overflow = '';
        openButtons.forEach((button) => button.setAttribute('aria-expanded', 'false'));
        if (returnFocus && opener?.isConnected) opener.focus();
    };
    const openDrawer = (button) => {
        if (desktop.matches) return;
        opener = button;
        open = true;
        drawer.hidden = false;
        drawer.inert = false;
        drawer.classList.add('is-open');
        drawer.setAttribute('role', 'dialog');
        drawer.setAttribute('aria-modal', 'true');
        backdrop.hidden = false;
        background.inert = true;
        document.body.style.overflow = 'hidden';
        openButtons.forEach((node) => node.setAttribute('aria-expanded', 'true'));
        (focusable()[0] || drawer).focus();
    };
    openButtons.forEach((button) => button.addEventListener('click', () => openDrawer(button)));
    drawer.querySelectorAll('[data-drawer-close]').forEach((button) => button.addEventListener('click', () => closeDrawer()));
    backdrop.addEventListener('click', () => closeDrawer());
    document.addEventListener('keydown', (event) => {
        if (!open) return;
        if (event.key === 'Escape') {
            event.preventDefault();
            closeDrawer();
        }
        if (event.key === 'Tab') {
            const nodes = focusable();
            if (!nodes.length) {
                event.preventDefault();
                drawer.focus();
            } else if (event.shiftKey && document.activeElement === nodes[0]) {
                event.preventDefault();
                nodes[nodes.length - 1].focus();
            } else if (!event.shiftKey && document.activeElement === nodes[nodes.length - 1]) {
                event.preventDefault();
                nodes[0].focus();
            }
        }
    });
    desktop.addEventListener('change', () => closeDrawer(false));
    closeDrawer(false);
}

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

const confirmation = document.getElementById('confirmation-dialog');
const approvedForms = new WeakSet();
let pendingConfirmation = null;
const dismissConfirmation = () => {
    const original = pendingConfirmation?.focusTarget;
    pendingConfirmation = null;
    confirmation?.close();
    if (original?.isConnected) original.focus();
};
if (confirmation) {
    confirmation.querySelector('[data-confirmation-cancel]').addEventListener('click', dismissConfirmation);
    confirmation.addEventListener('cancel', (event) => {
        event.preventDefault();
        dismissConfirmation();
    });
    confirmation.addEventListener('click', (event) => {
        if (event.target !== confirmation) return;
        const bounds = confirmation.getBoundingClientRect();
        if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) dismissConfirmation();
    });
    confirmation.querySelector('[data-confirmation-approve]').addEventListener('click', () => {
        if (!pendingConfirmation) return;
        const { form, submitter } = pendingConfirmation;
        pendingConfirmation = null;
        confirmation.close();
        approvedForms.add(form);
        if (submitter?.form === form && !submitter.disabled) form.requestSubmit(submitter);
        else form.requestSubmit();
    });
}

document.querySelectorAll('form').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (form.classList.contains('is-submitting')) {
            event.preventDefault();
            return;
        }
        if (form.dataset.confirm && !approvedForms.has(form)) {
            if (confirmation && typeof confirmation.showModal === 'function') {
                event.preventDefault();
                pendingConfirmation = { form, submitter: event.submitter, focusTarget: event.submitter || document.activeElement };
                confirmation.querySelector('[data-confirmation-description]').textContent = form.dataset.confirm;
                confirmation.showModal();
                confirmation.querySelector('[data-confirmation-cancel]').focus();
                return;
            }
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
                return;
            }
        }
        approvedForms.delete(form);
        if (form.method.toLowerCase() === 'post') {
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
