const { __GISTPEN_I18N__ = {} } = global;

/**
 * Get the translation string for the key.
 *
 * @param {string} key - Translation key.
 * @returns {string} Translation string.
 */
export default function i18n(key) {
    return __GISTPEN_I18N__[key] || __GISTPEN_I18N__['i18n.notfound'] || 'Translation & fallback not found for key ' + key;
}
