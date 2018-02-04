// @flow
import type { Reducer } from 'redux';
import type { SearchInputAction } from '../types';
import { combineActionReducers } from 'brookjs';
import { SEARCH_INPUT, SEARCH_RESULTS_SUCCEEDED } from '../actions';

export type AjaxState = {
    running: boolean
};

const defaults : AjaxState = {
    running: false
};

const cond = [
    [SEARCH_INPUT, (state: AjaxState, { payload }: SearchInputAction) => ({ ...state, running: !!payload.value })],
    [SEARCH_RESULTS_SUCCEEDED, (state: AjaxState) => ({ ...state, running: false })]
];

export const ajaxReducer : Reducer<AjaxState, *> = combineActionReducers(cond, defaults);
