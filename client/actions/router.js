// @flow
import type { RouteChangeAction, RouteParts } from '../types';

/**
 * Dispatched when route changes.
 */
export const ROUTE_CHANGE = 'ROUTE_CHANGE';

export const routeChangeAction = (name : string, parts : RouteParts = {}) : RouteChangeAction => ({
    type: ROUTE_CHANGE,
    payload: { name, parts }
});
