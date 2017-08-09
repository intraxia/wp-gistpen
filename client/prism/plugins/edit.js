import Prism from '../';

/**
 * Create an edit button for the provided environment.
 *
 * @param {Object} env - Prism environment.
 * @returns {Element} Edit button element.
 */
Prism.plugins.toolbar.registerButton('edit', function editButton(env) {
    const pre = env.element.parentElement;

    if (!pre.hasAttribute('data-edit-url')) {
        return;
    }

    const editBtn = document.createElement('a');
    editBtn.innerHTML = 'Edit';
    editBtn.href = pre.getAttribute('data-edit-url');

    return editBtn;
});

export const plugin = {
    use() {

    },

    unuse() {

    }
};
