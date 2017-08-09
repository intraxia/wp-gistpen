// @flow
import langs from '../../config/languages.json';

/**
 * Map a language slug to its prism slug.
 *
 * @param {string} slug - Language slug.
 * @returns {string} Prism slug.
 */
export default function prismSlug(slug : string) : string {
    return langs.aliases[slug] || slug;

};
