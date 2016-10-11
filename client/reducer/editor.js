import { EDITOR_OPTIONS_CLICK } from '../action';

const defaults = {
    optionsOpen: false
};

/**
 * Returns a new editor state for a given action.
 *
 * @param {Object} state - Current editor state.
 * @param {string} type - Action type.
 * @returns {Object} New editor state.
 */
export default function editorReducer(state = defaults, { type } = {}) {
    switch (type) {
        case EDITOR_OPTIONS_CLICK:
            return Object.assign({}, state, { optionsOpen: !state.optionsOpen });
        default:
            return state;
    }
};
