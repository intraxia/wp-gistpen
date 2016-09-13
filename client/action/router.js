export const ROUTE_CHANGE = 'ROUTE_CHANGE';

export function routeChangeAction(route, parts = {}) {
    return {
        type: ROUTE_CHANGE,
        payload: { route, parts }
    };
}
