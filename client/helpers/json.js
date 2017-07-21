/**
 * Handlebars helper to stringify data to json.
 *
 * Useful for debugging.
 *
 * @param {*} value - Value to stringify.
 * @returns {string} Stringified value..
 */
export default function json(value) {
    return JSON.stringify(value, null, '  ');
};
