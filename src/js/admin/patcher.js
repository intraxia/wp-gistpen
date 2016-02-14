import assignDeep from 'object-assign-deep';
import equal from 'deep-equal';
import {
    UPDATE_PRISM_THEME,
    TOGGLE_LINE_NUMBERS,
    TOGGLE_SHOW_INVISIBLES,
    UPDATE_GIST_TOKEN,
    RESET_SITE_STATE
} from './actions';

/**
 * Converts an action event into a patch.
 *
 * @param {Object} event
 * @returns {Object}
 */
export default function (event) {
    switch (event.type) {
        case UPDATE_PRISM_THEME:
            return {site: {prism: {theme: event.theme}}};
        case TOGGLE_LINE_NUMBERS:
            return {site: {prism: {'line-numbers': event.enabled}}};
        case TOGGLE_SHOW_INVISIBLES:
            return {site: {prism: {'show-invisibles': event.enabled}}};
        case UPDATE_GIST_TOKEN:
            return {site: {gist: {token: event.token}}};
    }

    return {};
}
