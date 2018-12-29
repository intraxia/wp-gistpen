// @flow
import type { GistState, GistTokenChangeAction } from '../types';
import combineActionReducers from './combineActionReducers';
import { GIST_TOKEN_CHANGE } from '../actions';

const defaults = { token: '' };

export default combineActionReducers([
    [GIST_TOKEN_CHANGE, (state: GistState, { payload }: GistTokenChangeAction) => ({
        ...state,
        token: payload.value
    })]
], defaults);
