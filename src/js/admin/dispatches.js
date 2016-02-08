import fetchival from 'fetchival';
import assignDeep from 'object-assign-deep';
import { polyfill } from 'es6-promise';
import { setAjaxStatus, AJAX } from '../wordpress/actions';
import {
    updatePrismTheme,
    toggleLineNumbers,
    toggleShowInvisibles,
    updateGistToken,
    resetSiteState
} from './actions';
import { dispatch } from './store';

if (!window.Promise) {
    polyfill();
}

const api = fetchival(`${window.Gistpen_Settings.root}site`, {
    credentials: 'include',
    headers: {
        'X-WP-Nonce': window.Gistpen_Settings.nonce
    }
});

export function handlePrismThemeChange(event) {
    return dispatch(
        updatePrismTheme(event.target.value)
    );
}

export function handleLineNumbersChange(event) {
    return dispatch(
        toggleLineNumbers(event.target.checked)
    );
}

export function handleShowInvisiblesChange(event) {
    return dispatch(
        toggleShowInvisibles(event.target.checked)
    );
}

export function handleGistTokenChange(event) {
    return dispatch(
        updateGistToken(event.target.value)
    );
}

let updating = false;
let timeout;

export function handleServerUpdate(patch) {
    if (updating) {
        return queueUpdate(patch);
    }

    if (timeout) {
        clearTimeout(timeout);
    }

    dispatch(setAjaxStatus(AJAX.UPDATING));

    api.patch(patch)
        // Update our site state from the server
        .then((site) => dispatch(resetSiteState(site)))
        // Notify the UI that update is complete
        .then(() => dispatch(setAjaxStatus(AJAX.SUCCESS)))
        // Error handling
        .catch(() => dispatch(setAjaxStatus(AJAX.ERROR)))
        .then(() => updating = false)
        // Wait then reset error message
        .then(() => new Promise((resolve) => timeout = setTimeout(resolve, 5000)))
        .then(() => dispatch(setAjaxStatus(AJAX.IDLE)));
}

let next;

function queueUpdate(patch) {
    next = assignDeep({}, next, patch);
    setTimeout(() => handleServerUpdate(next), 500);
}
