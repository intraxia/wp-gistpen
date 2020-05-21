import Prism from 'prismjs';

/**
 * Create a filename label for the provided environment.
 *
 * @param {Object} env - Prism environment.
 * @returns {Element} Edit button element.
 */
function filenameButton(env: Prism.Environment) {
  const filename = document.createElement('span');
  const pre = env.element?.parentElement;

  if (pre == null) {
    return;
  }

  const text = pre.getAttribute('data-filename');

  if (text == null) {
    return;
  }

  filename.textContent = text;

  return filename;
}

export const plugin = {
  use() {
    Prism.plugins.toolbar.registerButton('filename', filenameButton);
  },

  unuse() {
    // @TODO(mAAdhaTTah) implement upstream
    // Prism.plugins.toolbar.unregisterButton('filename', filenameButton);
  },
};
