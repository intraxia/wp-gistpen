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
export const editorThemeChangeAction = function editorThemeChangeAction(value) {
    return {
        type: EDITOR_THEME_CHANGE,
        payload: { value }
    };
};

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
export const editorTabsToggleAction = function editorTabsToggleAction(value) {
    return {
        type: EDITOR_TABS_TOGGLE,
        payload: { value }
    };
};

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
export const editorWidthChangeAction = function editorWidthChangeAction(value) {
    return {
        type: EDITOR_WIDTH_CHANGE,
        payload: { value }
    };
};

/**
 * Dispatched when the Editor enables or disables invisibles.
 *
 * @type {string}
 */
export const EDITOR_INVISIBLES_TOGGLE = 'EDITOR_INVISIBLES_TOGGLE';

/**
 * Creates a new Editor Invisibles Toggle Action.
 *
 * @param {string} value - Editor theme.
 * @returns {Action} Editor Options Click Action
 */
export const editorInvisiblesToggleAction = function editorInvisiblesToggleAction(value) {
    return {
        type: EDITOR_INVISIBLES_TOGGLE,
        payload: { value }
    };
};

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
export const editorUpdateClickAction = function editorUpdateClickAction() {
    return { type: EDITOR_UPDATE_CLICK };
};

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
export const editorDescriptionChangeAction = function editorDescriptionChangeAction(value) {
    return {
        type: EDITOR_DESCRIPTION_CHANGE,
        payload: { value }
    };
};

/**
 * Dispatched when the Editor status changes.
 *
 * @type {string}
 */
export const EDITOR_STATUS_CHANGE = 'EDITOR_STATUS_CHANGE';

/**
 * Creates a new Editor Status Change Action.
 *
 * @param {string} value - Editor status..
 * @returns {Action} Editor Status Change Action.
 */
export const editorStatusChangeAction = function editorStatusChangeAction(value) {
    return {
        type: EDITOR_STATUS_CHANGE,
        payload: { value }
    };
};

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
export const editorSyncToggleAction = function editorSyncToggleAction(value) {
    return {
        type: EDITOR_SYNC_TOGGLE,
        payload: { value }
    };
};

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
export const editorFilenameChangeAction = function editorFilenameChangeAction(value) {
    return {
        type: EDITOR_FILENAME_CHANGE,
        payload: { value }
    };
};


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
 * @param {string} add - Characters added.
 * @param {string} del - Characters deleted.
 * @param {Cursor} cursor - Selection of the cursor.
 * @returns {Action} Editor Value Change Action.
 */
export const editorValueChangeAction = function editorValueChangeAction({ code, cursor, add = '', del = '' }) {
    return {
        type: EDITOR_VALUE_CHANGE,
        payload: { code, cursor, add, del }
    };
};

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
export const editorIndentAction = function editorIndentAction({ code, cursor, inverse }) {
    return {
        type: EDITOR_INDENT,
        payload: { code, cursor, inverse }
    };
};

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
export const editorMakeCommentAction = function editorMakeCommentAction({ code, cursor }) {
    return {
        type: EDITOR_MAKE_COMMENT,
        payload: { code, cursor }
    };
};

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
export const editorMakeNewlineAction = function editorMakeNewlineAction({ code, cursor }) {
    return {
        type: EDITOR_MAKE_NEWLINE,
        payload: { code, cursor }
    };
};

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
export const editorRedoAction = function editorRedoAction() {
    return { type: EDITOR_REDO };
};

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
export const editorUndoAction = function editorUndoAction () {
    return { type: EDITOR_UNDO };
};

/**
 * Emitted when the editor's cursor moves.
 *
 * @type {string}
 */
export const EDITOR_CURSOR_MOVE = 'EDITOR_CURSOR_MOVE';

/**
 * Creates a new Editor Cursor Move Action.
 *
 * @param {false|Cursor} cursor - Cursor position.
 * @returns {Action} Editor Cursor Move Action.
 */
export const editorCursorMoveAction = function editorCaretMoveAction(cursor) {
    return {
        type: EDITOR_CURSOR_MOVE,
        payload: { cursor }
    };
};
