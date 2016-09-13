import { ROUTE_CHANGE } from '../action';

/**
 * Update the route value.
 *
 * @param {string} state - Current route.
 * @param {string} type - Action type.
 * @param {Object] payload - Action payload.
 * @returns {string} New route.
 */
export default function routeReducer(state = '', { type, payload }) {
    switch (type) {
        case ROUTE_CHANGE:
            return payload.route;
        default:
            return state;
    }
}
