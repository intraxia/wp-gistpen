import { bootstrap } from './admin';

/**
 * Kick off the Settings page application
 * with the contextual requirements.
 */
bootstrap(
    Object.assign({}, window.Gistpen_Settings),
    document.getElementById('wpgp-wrap')
);
