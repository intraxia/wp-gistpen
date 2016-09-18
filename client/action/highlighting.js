/**
 * Dispatched when theme changes.
 *
 * @type {string}
 */
export const THEME_CHANGE = 'THEME_CHANGE';

/**
 * Creates a theme change action.
 *
 * @param {string} value - Theme value.
 * @returns {Action} Theme change action.
 */
export function themeChangeAction(value) {
    return {
        type: THEME_CHANGE,
        payload: { value }
    };
}

/**
 * Dispatched when line numbers gets enabled/disabled.
 *
 * @type {string}
 */
export const LINE_NUMBERS_CHANGE = 'LINE_NUMBERS_CHANGE';

/**
 * Creates a line numbers change action.
 *
 * @param {boolean} value - Whether line numbers is enabled.
 * @returns {Action} Line numbers action.
 */
export function lineNumbersChangeAction(value) {
    return {
        type: LINE_NUMBERS_CHANGE,
        payload: { value }
    };
}

/**
 * Dispatched when show invisibles gets enabled/disabled.
 *
 * @type {string}
 */
export const SHOW_INVISIBLES_CHANGE = 'SHOW_INVISIBLES_CHANGE';

/**
 * Create a show invisibles change action.
 *
 * @param {boolean} value - Whether show invisibles is enabled.
 * @returns {Action} Show invisibles action.
 */
export function showInvisiblesChangeAction(value) {
    return {
        type: SHOW_INVISIBLES_CHANGE,
        payload: { value }
    };
}
