import type { Action } from '../actions';
import type { Repo } from '../types';
import type { TinyMCEState } from './tinyMCE';
import { INIT } from '../actions';

export type GlobalsState = {|
    languages : {[key : string] : string; };
    root : string;
    nonce : string;
    url : string;
    ace_widths : Array<number>;
    statuses : { [key : string] : string; };
    themes : { [key : string] : string; };
    repo? : Repo;
|};

const defaults : GlobalsState = {
    languages: {},
    root: '',
    nonce: '',
    url: '',
    ace_widths: [],
    statuses: {},
    themes: {},
};

export const globalsReducer = (state : GlobalsState = defaults, action : Action<TinyMCEState>) : GlobalsState => {
    switch (action.type) {
        case INIT:
            return {
                ...state,
                ...action.payload.initial.globals
            };
        default:
            return state;
    }
};
