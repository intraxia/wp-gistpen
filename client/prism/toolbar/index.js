import styles from './styles.scss';

import clipboard from './clipboard';
import edit from './edit';
import filename from './filename';

const callbacks = [clipboard, edit, filename];

styles.use();

/**
 * Post-highlight Prism hook callback.
 *
 * @param {Object} env - Prism environment.
 */
export default function hook(env) {
    // Check if inline or actual code block,
    // credit to line-numbers plugin.
    var pre = env.element.parentNode;
    if (!pre || !/pre/i.test(pre.nodeName)) {
        return;
    }

    pre.classList.add('code-toolbar');

    // Setup the toolbar
    var toolbar = document.createElement('div');
    toolbar.classList.add('toolbar');

    callbacks.forEach(callback => {
        const element = callback(env);

        if (!element) {
            return;
        }

        const item = document.createElement('div');
        item.classList.add('toolbar-item');

        item.appendChild(element);
        toolbar.appendChild(item);
    });

    // Add our toolbar to the <pre> tag
    pre.appendChild(toolbar);
}
