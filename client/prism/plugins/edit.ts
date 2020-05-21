import Prism from 'prismjs';

function editButton(env: Prism.Environment) {
  const pre = env.element?.parentElement;

  if (pre == null) {
    return;
  }

  const url = pre.getAttribute('data-edit-url') ?? null;

  if (url == null) {
    return;
  }

  const editBtn = document.createElement('a');
  editBtn.innerHTML = 'Edit';
  editBtn.href = url;

  return editBtn;
}

export const plugin = {
  use() {
    Prism.plugins.toolbar.registerButton('edit', editButton);
  },

  unuse() {
    // @TODO(mAAdhaTTah) implement upstream
    // Prism.plugins.toolbar.unregisterButton('edit', editButton);
  },
};
