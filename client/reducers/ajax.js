// @flow
import type { Reducer } from 'redux';
import { combineActionReducers } from 'brookjs';

export type AjaxState = {
    running: boolean
};

const defaults : AjaxState = {
    running: false
};

const cond = [

];

export const ajaxReducer : Reducer<AjaxState, *> = combineActionReducers(cond, defaults);
