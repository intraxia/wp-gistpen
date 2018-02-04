// @flow
import type { InitAction } from '../actions';
import type { Repo } from '../types';
import { combineActionReducers } from 'brookjs';
import { INIT } from '../actions';

export type GlobalsState = {
    languages: {[key: string]: string };
    root: string;
    nonce: string;
    url: string;
    ace_widths: Array<number>;
    statuses: { [key: string]: string };
    themes: { [key: string]: string };
    repo?: Repo
};

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
