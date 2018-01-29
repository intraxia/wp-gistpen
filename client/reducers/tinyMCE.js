// @flow
import type { Reducer } from 'redux';
import type { SearchState } from './search';
import type { AjaxState } from './ajax';
import type { GlobalsState } from './globals';
import { combineReducers } from 'redux';
import { ajaxReducer } from './ajax';
import { globalsReducer } from './globals';
import { searchReducer } from './search';

export type TinyMCEState = {
    ajax : AjaxState;
    globals : GlobalsState;
    search : SearchState;
};

export const tinyMCEReducer : Reducer<TinyMCEState, *> = combineReducers({
    ajax: ajaxReducer,
    globals: globalsReducer,
    search: searchReducer
});
