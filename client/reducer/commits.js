// @flow
import type { CommitsFetchSucceededAction, CommitsState } from '../type';
import { combineActionReducers } from 'brookjs';
import { COMMITS_FETCH_SUCCEEDED } from '../action';

const cond = [
    [COMMITS_FETCH_SUCCEEDED, (state : CommitsState, action : CommitsFetchSucceededAction) => ({
        ...state,
        instances: action.payload.response
    })]
];

const defaults : CommitsState = {
    instances: []
};

export default combineActionReducers(cond, defaults);
