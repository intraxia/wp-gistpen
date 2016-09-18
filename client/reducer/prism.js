import { THEME_CHANGE, LINE_NUMBERS_CHANGE, SHOW_INVISIBLES_CHANGE } from '../action';

const defaults = {
    theme: 'default',
    'line-numbers': false,
    'show-invisibles': false
};

/**
 * Updates the prism state.
 *
 * @param {Object} state - Current state.
 * @param {string} type - Action type.
 * @param {Object} payload - Action payload.
 * @returns {Object} New state.
 */
export default function prismReducer(state = defaults, { type, payload = {} }) {
    const { value } = payload;

    switch (type) {
        case THEME_CHANGE:
            return Object.assign({}, state, { theme: value });
        case LINE_NUMBERS_CHANGE:
            return Object.assign({}, state, { 'line-numbers': value });
        case SHOW_INVISIBLES_CHANGE:
            return Object.assign({}, state, { 'show-invisibles': value });
        default:
            return state;
    }
}
