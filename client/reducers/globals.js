// @flow
import type { InitAction } from '../actions';
import type { GlobalsState } from '../types';
import { combineActionReducers } from 'brookjs';
import { INIT } from '../actions';

const defaults : GlobalsState = {
    languages: {},
    root: '',
    nonce: '',
    url: '',
    ace_widths: [],
    statuses: {},
    themes: {},
};

const cond = [
    [INIT, (state: GlobalsState = defaults, { payload }: InitAction<{ globals: GlobalsState }>): GlobalsState => ({
        ...state,
        ...payload.initial.globals
    })]
];

export const globalsReducer = combineActionReducers(cond, defaults);
