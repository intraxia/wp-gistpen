// @flow
import type { ThemeChangeAction, LineNumbersChangeAction,
    ShowInvisiblesChangeAction } from '../types';

/**
 * Dispatched when theme changes.
 *
 * @type {string}
 */
export const THEME_CHANGE : string = 'THEME_CHANGE';

/**
 * Creates a theme change actions.
 *
 * @param {string} value - Theme value.
 * @returns {Action} Theme change actions.
 */
export function themeChangeAction(value: string): ThemeChangeAction {
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
export const LINE_NUMBERS_CHANGE : string = 'LINE_NUMBERS_CHANGE';

/**
 * Creates a line numbers change actions.
 *
 * @param {boolean} value - Whether line numbers is enabled.
 * @returns {Action} Line numbers actions.
 */
export function lineNumbersChangeAction(value: boolean): LineNumbersChangeAction {
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
export const SHOW_INVISIBLES_CHANGE : string = 'SHOW_INVISIBLES_CHANGE';

/**
 * Create a show invisibles change actions.
 *
 * @param {boolean} value - Whether show invisibles is enabled.
 * @returns {Action} Show invisibles actions.
 */
export function showInvisiblesChangeAction(value: boolean): ShowInvisiblesChangeAction {
    return {
        type: SHOW_INVISIBLES_CHANGE,
        payload: { value }
    };
}
