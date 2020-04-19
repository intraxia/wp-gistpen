import { AjaxOptions } from '../ajax';
import { UserDeltaState } from '../deltas';

export const selectUserAjaxOpts = (state: UserDeltaState): AjaxOptions => ({
  method: 'PATCH',
  body: JSON.stringify({
    editor: {
      theme: state.editor.theme,
      invisibles_enabled: state.editor.invisibles,
      tabs_enabled: state.editor.tabs,
      indent_width: state.editor.width,
    },
  }),
  credentials: 'include',
  headers: {
    'X-WP-Nonce': state.globals.nonce,
    'Content-Type': 'application/json',
  },
});
