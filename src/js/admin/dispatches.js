import fetchival from 'fetchival';
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

let timeout;

export function handleServerUpdate(patch) {
    if (timeout) {
        return;
    }

    dispatch(setAjaxStatus(AJAX.UPDATING));

    api.patch(patch)
        // Update our site state from the server
        .then((site) => dispatch(resetSiteState(site)))
        // Notify the UI that update is complete
        .then(() => dispatch(setAjaxStatus(AJAX.SUCCESS)))
        // Error handling
        .catch(() => dispatch(setAjaxStatus(AJAX.ERROR)))
        // Wait then reset error message
        .then(() => new Promise((resolve) => timeout = setTimeout(resolve, 5000)))
        .then(() => dispatch(setAjaxStatus(AJAX.IDLE)));
}
