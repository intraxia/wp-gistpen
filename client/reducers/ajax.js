// @flow
import type { Reducer } from 'redux';
import type { AjaxState, SearchInputAction } from '../types';
import { combineActionReducers } from 'brookjs';
import { AJAX_STARTED, AJAX_FAILED, AJAX_FINISHED,
    SEARCH_INPUT, SEARCH_RESULTS_SUCCEEDED } from '../actions';

const defaults : AjaxState = {
    running: false
};

const cond = [
    [AJAX_STARTED, (state: AjaxState) => ({ ...state, running: true })],
    [AJAX_FAILED, (state: AjaxState) => ({ ...state, running: false })],
    [AJAX_FINISHED, (state: AjaxState) => ({ ...state, running: false })],
    [SEARCH_INPUT, (state: AjaxState, { payload }: SearchInputAction) => ({ ...state, running: !!payload.value })],
    [SEARCH_RESULTS_SUCCEEDED, (state: AjaxState) => ({ ...state, running: false })]
];

export const ajaxReducer : Reducer<AjaxState, *> = combineActionReducers(cond, defaults);
