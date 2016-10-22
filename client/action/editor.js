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
