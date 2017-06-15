import Prism from '../';

/**
 * Create a filename label for the provided environment.
 *
 * @param {Object} env - Prism environment.
 * @returns {Element} Edit button element.
 */
Prism.plugins.toolbar.registerButton('filename', function filenameButton(env) {
    const filename = document.createElement('span');
    const pre = env.element.parentElement;

    if (!pre.hasAttribute('data-filename')) {
        return;
    }

    filename.innerHTML = pre.getAttribute('data-filename');

    return filename;
});

export const plugin = {
    use() {

    },

    unuse() {

    }
};
