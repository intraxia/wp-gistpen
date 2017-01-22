import { REPO_SAVE_SUCCEEDED } from '../action';

/**
 * Updates the repo state.
 *
 * @param {Object} state - Current state.
 * @param {string} type - Action type.
 * @param {Object} payload - Action payload.
 * @returns {Object} New state.
 */
export default function repoReducer(state = {}, { type, payload }) {
    switch (type) {
        case REPO_SAVE_SUCCEEDED:
            return payload.response;
        default:
            return state;
    }
}
