// @flow
import type { Route, RouteChangeAction } from '../type';
import { combineActionReducers } from 'brookjs';
import { ROUTE_CHANGE } from '../action';

export default combineActionReducers([
    [ROUTE_CHANGE, (state : Route, { payload } : RouteChangeAction) : Route => payload]
], '');
