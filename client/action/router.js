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
 * @param {Object} [parts] - Additional route parts.
 * @returns {Action} Route change action.
 */
export function routeChangeAction(route, parts = {}) {
    return {
        type: ROUTE_CHANGE,
        payload: { route, parts }
    };
}
