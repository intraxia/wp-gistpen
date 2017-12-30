// @flow
import type { Route, RouteChangeAction } from '../types';
import { combineActionReducers } from 'brookjs';
import { ROUTE_CHANGE } from '../actions';

export default combineActionReducers([
    [ROUTE_CHANGE, (state : Route, { payload } : RouteChangeAction) : Route => payload]
], '');
