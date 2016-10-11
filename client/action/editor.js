/**
 * Dispatched when the editor options button is clicked.
 *
 * @type {string}
 */
export const EDITOR_OPTIONS_CLICK = 'EDITOR_OPTIONS_CLICK';

/**
 * Creates a new Editor Options Click Action.
 *
 * @returns {Action} Editor Options Click Action
 */
export const editorOptionsClickAction = function editorOptionsClickAction() {
    return { type: EDITOR_OPTIONS_CLICK };
};
