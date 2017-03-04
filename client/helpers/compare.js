/**
 * Handlebars helper to compare two values.
 *
 * @param {*} first - First value
 * @param {*} second - Second value.
 * @param {Object} options - Handlebars options.
 * @returns {string} Template chunk.
 */
export default function compare(first, second, options) {
    if (first === second) {
        return options.fn(this);
    } else {
        return options.inverse(this);
    }
};
