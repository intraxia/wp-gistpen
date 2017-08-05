// @flow
import type { RunsState, RunsFetchSucceededAction } from '../type';
import R from 'ramda';
import { combineActionReducers } from 'brookjs';
import { RUNS_FETCH_SUCCEEDED } from '../action';

const defaults : RunsState = [];

const cond = [
    [RUNS_FETCH_SUCCEEDED, (state : RunsState, action : RunsFetchSucceededAction) : RunsState => {
        const newState = state.concat(action.payload.response);

        return R.uniqBy(R.prop('ID'), newState);
    }]
];

export default combineActionReducers(cond, defaults);
