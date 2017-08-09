// @flow
import type { PrismState, LineNumbersChangeAction, ShowInvisiblesChangeAction,
    ThemeChangeAction } from '../type';
import { THEME_CHANGE, LINE_NUMBERS_CHANGE, SHOW_INVISIBLES_CHANGE } from '../action';
import { combineActionReducers } from 'brookjs';

const defaults : PrismState = {
    theme: 'default',
    'line-numbers': false,
    'show-invisibles': false
};

const themeChangeReducer = (state : PrismState, action : ThemeChangeAction) : PrismState => ({
    ...state,
    theme: action.payload.value
});

const lineNumbersChangeReducer = (state : PrismState, action : LineNumbersChangeAction) : PrismState => ({
    ...state,
    'line-numbers': action.payload.value
});

const showInvisiblesChangeReducer = (state : PrismState, action : ShowInvisiblesChangeAction) : PrismState => ({
    ...state,
    'show-invisibles': action.payload.value
});

/**
 * Updates the prism state.
 *
 * @param {PrismState} state - Current state.
 * @param {action} action - Dispatched action.
 * @returns {PrismState} New state.
 */
export default combineActionReducers([
    [THEME_CHANGE, themeChangeReducer],
    [LINE_NUMBERS_CHANGE, lineNumbersChangeReducer],
    [SHOW_INVISIBLES_CHANGE, showInvisiblesChangeReducer]
], defaults);
