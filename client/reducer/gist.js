// @flow
import type { GistState, GistTokenChangeAction } from '../type';
import { combineActionReducers } from 'brookjs';
import { GIST_TOKEN_CHANGE } from '../action';

const defaults = { token: '' };

export default combineActionReducers([
    [GIST_TOKEN_CHANGE, (state : GistState, { payload } : GistTokenChangeAction) => ({
        ...state,
        token: payload.value
    })]
], defaults);
