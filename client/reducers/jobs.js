// @flow
import type { JobsState, JobFetchSucceededAction } from '../types';
import combineActionReducers from './combineActionReducers';
import { JOB_FETCH_SUCCEEDED } from '../actions';

const defaults : JobsState = {};

const cond = [
    [JOB_FETCH_SUCCEEDED, (state: JobsState, action: JobFetchSucceededAction) => ({
        ...state,
        [action.payload.response.slug]: action.payload.response
    })]
];

export default combineActionReducers(cond, defaults);
