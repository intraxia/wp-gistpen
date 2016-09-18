import { GIST_TOKEN_CHANGE } from '../action';

const defaults = { token: '' };

/**
 * Updates the gist state.
 *
 * @param {Object} state - Gist state.
 * @param {string} type - Action type.
 * @param {Object} payload - Action payload.
 * @returns {Object} New gist state.
 */
export default function gistReducer(state = defaults, { type, payload }) {
    switch (type) {
        case GIST_TOKEN_CHANGE:
            return Object.assign({}, state, { token: payload.value });
        default:
            return state;
    }
}
