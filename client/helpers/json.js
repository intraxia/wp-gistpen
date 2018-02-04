// @flow

/**
 * Handlebars helper to stringify data to json.
 *
 * Useful for debugging.
 *
 * @param {*} value - Value to stringify.
 * @returns {string} Stringified value..
 */
export default function json(value: Object | Array<any>): string {
    return JSON.stringify(value, null, '  ');
};
