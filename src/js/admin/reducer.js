import { SET_AJAX_STATUS } from '../wordpress/actions';
import {
    UPDATE_PRISM_THEME,
    TOGGLE_LINE_NUMBERS,
    TOGGLE_SHOW_INVISIBLES,
    UPDATE_GIST_TOKEN,
    RESET_SITE_STATE
} from './actions';

export default function(event, state) {
    let newState = JSON.parse(JSON.stringify(state));

    switch (event.type) {
        case UPDATE_PRISM_THEME:
            newState.site.prism.theme = event.theme;
            break;
        case TOGGLE_LINE_NUMBERS:
            newState.site.prism['line-numbers'] = event.enabled;
            break;
        case TOGGLE_SHOW_INVISIBLES:
            newState.site.prism['show-invisibles'] = event.enabled;
            break;
        case UPDATE_GIST_TOKEN:
            newState.site.gist.token = event.token;
            break;
        case RESET_SITE_STATE:
            newState.site = event.site;
            break;
        case SET_AJAX_STATUS:
            newState.ajax = event.status;
            break;
        default:
            // Nothing changed.
            newState = state;
            break;
    }

    return newState;
}
