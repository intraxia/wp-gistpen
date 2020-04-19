import Prism from '../';

Prism.plugins.toolbar.registerButton('edit', function editButton(env) {
  const pre = env.element.parentElement;

  if (pre == null) {
    return;
  }

  const url = pre.getAttribute('data-edit-url') || null;

  if (url == null) {
    return;
  }

  const editBtn = document.createElement('a');
  editBtn.innerHTML = 'Edit';
  editBtn.href = url;

  return editBtn;
});

export const plugin = {
  use() {},

  unuse() {},
};
