// @flow
import type { RouteState, RouteChangeAction } from '../type';
import { combineActionReducers } from 'brookjs';
import { ROUTE_CHANGE } from '../action';

export default combineActionReducers([
    [ROUTE_CHANGE, (state : RouteState, { payload } : RouteChangeAction) => payload.route]
], '');
