import { sprintf } from 'sprintf-js';
const { __GISTPEN_I18N__ = {} } = global;

/**
 * Get the translation string for the key.
 *
 * @param {string} key - Translation key.
 * @returns {string} Translation string.
 */
export default function i18n(key, ...args) {
    if (__GISTPEN_I18N__[key]) {
        return sprintf(__GISTPEN_I18N__[key], ...args);
    }

    return sprintf(
        __GISTPEN_I18N__['i18n.notfound'] ||
            'Translation & fallback not found for key %s',
        key
    );
}
