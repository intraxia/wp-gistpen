// @flow
import type { CommitsFetchSucceededAction, RevisionsState } from '../type';
import { combineActionReducers } from 'brookjs';
import { COMMITS_FETCH_SUCCEEDED } from '../action';

const cond = [
    [COMMITS_FETCH_SUCCEEDED, (state : RevisionsState, action : CommitsFetchSucceededAction) => ({
        ...state,
        instances: action.payload.response
    })]
];

const defaults : RevisionsState = {
    instances: []
};

export default combineActionReducers(cond, defaults);
