// @flow
import type { EditorPageState } from '../types';
import type { AjaxOptions } from '../services';

/**
 * Map the editor pages state to the Ajax options for the user endpoint.
 *
 * @param {EditorPageState} state - Current editor pages state.
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
            'X-WP-Nonce': state.globals.nonce,
            'Content-Type': 'application/json'
        }
    };
}
