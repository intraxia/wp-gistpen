// @flow
import type {
    Cursor,
    EditorAddClickAction,
    EditorCursorMoveAction,
    EditorDeleteClickAction,
    EditorDescriptionChangeAction,
    EditorFilenameChangeAction,
    EditorIndentAction,
    EditorIndentValue,
    EditorInvisiblesToggleAction,
    EditorLanguageChangeAction,
    EditorMakeCommentAction,
    EditorMakeNewLineAction,
    EditorOptionsClickAction,
    EditorRedoAction,
    EditorStatusChangeAction,
    EditorSyncChangeAction,
    EditorTabsToggleAction,
    EditorThemeChangeAction,
    EditorUndoAction,
    EditorUpdateClickAction,
    EditorValue,
    EditorValueChangeAction,
    EditorWidthChangeAction,
    Toggle
} from '../types';

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
export function editorOptionsClickAction(): EditorOptionsClickAction {
    return { type: EDITOR_OPTIONS_CLICK };
}

/**
 * Dispatched when the Editor theme changes.
 *
 * @type {string}
 */
export const EDITOR_THEME_CHANGE = 'EDITOR_THEME_CHANGE';

/**
 * Creates a new Editor Options Click Action.
 *
 * @param {string} value - Editor theme.
 * @returns {Action} Editor Options Click Action
 */
export function editorThemeChangeAction(value: string): EditorThemeChangeAction {
    return {
        type: EDITOR_THEME_CHANGE,
        payload: { value }
    };
}

/**
 * Dispatched when the Editor switches between tabs and spaces.
 *
 * @type {string}
 */
export const EDITOR_TABS_TOGGLE = 'EDITOR_TABS_TOGGLE';

/**
 * Creates a new Editor Tabs Change Action.
 *
 * @param {string} value - Editor tabs enabled status.
 * @returns {Action} Editor Options Click Action
 */
export function editorTabsToggleAction(value: string): EditorTabsToggleAction {
    return {
        type: EDITOR_TABS_TOGGLE,
        payload: { value }
    };
}

/**
 * Dispatched when the Editor indentation width changes.
 *
 * @type {string}
 */
export const EDITOR_WIDTH_CHANGE = 'EDITOR_WIDTH_CHANGE';

/**
 * Creates a new Editor Width Change Action.
 *
 * @param {string} value - Editor indentation width.
 * @returns {Action} Editor Options Click Action
 */
export function editorWidthChangeAction(value: string): EditorWidthChangeAction {
    return {
        type: EDITOR_WIDTH_CHANGE,
        payload: { value }
    };
}

/**
 * Dispatched when the Editor enables or disables invisibles.
 *
 * @type {string}
 */
export const EDITOR_INVISIBLES_TOGGLE = 'EDITOR_INVISIBLES_TOGGLE';

/**
 * Creates a new Editor Invisibles Toggle Action.
 *
 * @param {Toggle} value - Editor invisibles toggle.
 * @returns {Action} Editor Options Click Action
 */
export function editorInvisiblesToggleAction(value: Toggle): EditorInvisiblesToggleAction {
    return {
        type: EDITOR_INVISIBLES_TOGGLE,
        payload: { value }
    };
}

/**
 * Dispatched when the Editor update button is clicked.
 *
 * @type {string}
 */
export const EDITOR_UPDATE_CLICK = 'EDITOR_UPDATE_CLICK';

/**
 * Creates a new Editor Update Click Action.
 *
 * @returns {Action} Editor Update Click Action.
 */
export function editorUpdateClickAction(): EditorUpdateClickAction {
    return { type: EDITOR_UPDATE_CLICK };
}

/**
 * Dispatched when the Editor add button is clicked.
 *
 * @type {string}
 */
export const EDITOR_ADD_CLICK = 'EDITOR_ADD_CLICK';

/**
 * Creates a new Editor Update Click Action.
 *
 * @returns {Action} Editor Update Click Action.
 */
export function editorAddClickAction(): EditorAddClickAction {
    return { type: EDITOR_ADD_CLICK };
}

/**
 * Dispatched when an editor instance delete button is clicked.
 *
 * @type {string}
 */
export const EDITOR_DELETE_CLICK = 'EDITOR_DELETE_CLICK';

/**
 * Creates a new Editor Instance Delete Click Action.
 *
 * @returns {Action} Editor Instance Delete Click Action.
 */
export function editorDeleteClickAction(): EditorDeleteClickAction {
    return { type: EDITOR_DELETE_CLICK };
}

/**
 * Dispatched when the Editor description changes.
 *
 * @type {string}
 */
export const EDITOR_DESCRIPTION_CHANGE = 'EDITOR_DESCRIPTION_CHANGE';

/**
 * Creates a new Editor Description Change Action.
 *
 * @param {string} value - Editor description.
 * @returns {Action} Editor Description Change Action.
 */
export function editorDescriptionChangeAction(value: string): EditorDescriptionChangeAction {
    return {
        type: EDITOR_DESCRIPTION_CHANGE,
        payload: { value }
    };
}

/**
 * Dispatched when the Editor status changes.
 *
 * @type {string}
 */
export const EDITOR_STATUS_CHANGE = 'EDITOR_STATUS_CHANGE';

/**
 * Creates a new Editor Status Change Action.
 *
 * @param {string} value - Editor status.
 * @returns {Action} Editor Status Change Action.
 */
export function editorStatusChangeAction(value: string): EditorStatusChangeAction {
    return {
        type: EDITOR_STATUS_CHANGE,
        payload: { value }
    };
}

/**
 * Dispatched when the Editor sync status changes.
 *
 * @type {string}
 */
export const EDITOR_SYNC_TOGGLE = 'EDITOR_SYNC_TOGGLE';

/**
 * Creates a new Editor Sync Change Action.
 *
 * @param {string} value - Editor sync status.
 * @returns {Action} Editor Sync Change Action.
 */
export function editorSyncToggleAction(value: Toggle): EditorSyncChangeAction {
    return {
        type: EDITOR_SYNC_TOGGLE,
        payload: { value }
    };
}

/**
 * Emitted when the value of a filename changes.
 *
 * @type {string}
 */
export const EDITOR_FILENAME_CHANGE = 'EDITOR_FILENAME_CHANGE';

/**
 * Creates a new Editor Filename Change Action.
 *
 * @param {string} value - Editor filename.
 * @returns {Action} Editor Filename Change Action.
 */
export function editorFilenameChangeAction(value: string): EditorFilenameChangeAction {
    return {
        type: EDITOR_FILENAME_CHANGE,
        payload: { value }
    };
}

/**
 * Emitted when the value of the language changes.
 *
 * @type {string}
 */
export const EDITOR_LANGUAGE_CHANGE = 'EDITOR_LANGUAGE_CHANGE';

/**
 * Creates a new Editor Language Change Action.
 *
 * @param {string} value - Editor language.
 * @returns {Action} Editor Filename Change Action.
 */
export function editorLanguageChangeAction(value: string): EditorLanguageChangeAction {
    return {
        type: EDITOR_LANGUAGE_CHANGE,
        payload: { value }
    };
}

/**
 * Emitted when the value in the editor changes.
 *
 * @type {string}
 */
export const EDITOR_VALUE_CHANGE = 'EDITOR_VALUE_CHANGE';

/**
 * Creates a new Editor Value Change Action.
 *
 * @param {string} code - New editor value.
 * @param {Cursor} cursor - Selection of the cursor.
 * @returns {Action} Editor Value Change Action.
 */
export function editorValueChangeAction({ code, cursor }: EditorValue): EditorValueChangeAction {
    return {
        type: EDITOR_VALUE_CHANGE,
        payload: { code, cursor }
    };
}

/**
 * Emitted when the user indents in the editor.
 *
 * @type {string}
 */
export const EDITOR_INDENT = 'EDITOR_INDENT_ACTION';

/**
 * Creates a new Editor Indent Action.
 *
 * @param {string} code - Editor value.
 * @param {Cursor} cursor - Cursor selection tuple.
 * @param {bool} inverse - Whether the intentation is inverted.
 * @returns {Action} Editor Indent Action.
 */
export function editorIndentAction({ code, cursor, inverse }: EditorIndentValue): EditorIndentAction {
    return {
        type: EDITOR_INDENT,
        payload: { code, cursor, inverse }
    };
}

/**
 * Emitted when the editor transforms a comment.
 *
 * @type {string}
 */
export const EDITOR_MAKE_COMMENT = 'EDITOR_MAKE_COMMENT';

/**
 * Creates a new Editor Make Comment Action.
 *
 * @param {string} code - Editor value.
 * @param {Cursor} cursor - Cursor selection tuple.
 * @returns {Action} Editor Make Comment Action.
 */
export function editorMakeCommentAction({ code, cursor }: EditorValue): EditorMakeCommentAction {
    return {
        type: EDITOR_MAKE_COMMENT,
        payload: { code, cursor }
    };
}

/**
 * Emitted when the editor creates a new line.
 *
 * @type {string}
 */
export const EDITOR_MAKE_NEWLINE = 'EDITOR_MAKE_NEWLINE';

/**
 * Creates a new Editor Make Newline Action.
 *
 * @param {string} code - Current editor value.
 * @param {Cursor} cursor - Cursor selection tuple.
 * @returns {Action} Editor Make Newline Action.
 */
export function editorMakeNewlineAction({ code, cursor }: EditorValue): EditorMakeNewLineAction {
    return {
        type: EDITOR_MAKE_NEWLINE,
        payload: { code, cursor }
    };
}

/**
 * Emitted when the editor wants an undo.
 *
 * @type {string}
 */
export const EDITOR_REDO = 'EDITOR_REDO';

/**
 * Creates a new Editor Redo Action.
 *
 * @returns {Action} Editor Redo Action.
 */
export function editorRedoAction(): EditorRedoAction {
    return { type: EDITOR_REDO };
}

/**
 * Emitted when the editor wants a redo.
 *
 * @type {string}
 */
export const EDITOR_UNDO = 'EDITOR_UNDO';

/**
 * Creates a new Editor Undo Action.
 *
 * @returns {Action} Editor Undo Action.
 */
export function editorUndoAction (): EditorUndoAction {
    return { type: EDITOR_UNDO };
}

/**
 * Emitted when the editor's cursor moves.
 *
 * @type {string}
 */
export const EDITOR_CURSOR_MOVE = 'EDITOR_CURSOR_MOVE';

/**
 * Creates a new Editor Cursor Move Action.
 *
 * @param {Cursor} cursor - Cursor position.
 * @returns {Action} Editor Cursor Move Action.
 */
export function editorCursorMoveAction(cursor: Cursor): EditorCursorMoveAction {
    return {
        type: EDITOR_CURSOR_MOVE,
        payload: { cursor }
    };
}
