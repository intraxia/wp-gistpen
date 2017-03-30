// @flow
import type { RouteChangeAction } from '../type';

/**
 * Dispatched when route changes.
 *
 * @type {string}
 */
export const ROUTE_CHANGE = 'ROUTE_CHANGE';

/**
 * Create a new Route change action.
 *
 * @param {string} route - Route changed to.
 * @returns {Action} Route change action.
 */
export function routeChangeAction(route : string) : RouteChangeAction {
    return {
        type: ROUTE_CHANGE,
        payload: { route }
    };
}
