// @flow
import type { JobsState, JobFetchSucceededAction } from '../type';
import { combineActionReducers } from 'brookjs';
import { JOB_FETCH_SUCCEEDED } from '../action';

const defaults : JobsState = {};

const cond = [
    [JOB_FETCH_SUCCEEDED, (state : JobsState, action : JobFetchSucceededAction) => ({
        ...state,
        [action.payload.response.slug]: action.payload.response
    })]
];

export default combineActionReducers(cond, defaults);
