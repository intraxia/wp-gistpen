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

  if (pre == null) {
    return;
  }

  const text = pre.getAttribute('data-filename') || null;

  if (text == null) {
    return;
  }

  filename.innerHTML = text;

  return filename;
});

export const plugin = {
  use() {},

  unuse() {}
};
