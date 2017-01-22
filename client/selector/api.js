// @flow
import type { AjaxOptions, EditorPageState } from '../type';

/**
 * Map the editor page state to the Ajax options for the user endpoint.
 *
 * @param {EditorPageState} state - Current editor page state.
 * @returns {AjaxOptions} Ajax options object.
 */
export function selectUserAjaxOpts(state : EditorPageState) : AjaxOptions {
    return {
        method: 'PATCH',
        body: JSON.stringify({
            editor: {
                theme: state.editor.theme,
                invisibles_enabled: state.editor.invisibles,
                tabs_enabled: state.editor.tabs,
                indent_width: state.editor.width
            }
        }),
        credentials: 'include',
        headers: {
            'X-WP-Nonce': state.api.nonce,
            'Content-Type': 'application/json'
        }
    };
}
