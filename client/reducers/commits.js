// @flow
import type { CommitsFetchSucceededAction, CommitsState } from '../types';
import type { CommitClickAction } from '../actions';
import { combineActionReducers } from 'brookjs';
import { COMMITS_FETCH_SUCCEEDED, COMMIT_CLICK } from '../actions';

const cond = [
    [COMMITS_FETCH_SUCCEEDED, (state: CommitsState, action: CommitsFetchSucceededAction) => ({
        ...state,
        instances: action.payload.response
    })],
    [COMMIT_CLICK, (state: CommitsState, action: CommitClickAction) => ({
        ...state,
        selected: parseInt(action.meta.key, 10) || null
    })]
];

const defaults : CommitsState = {
    instances: [],
    selected: null
};

export default combineActionReducers(cond, defaults);
