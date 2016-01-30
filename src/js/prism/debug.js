var debug = false;

/**
 * Set whether we're in debug mode.
 *
 * @param {bool} bool
 */
exports.setDebug = function setDebug(bool) {
    var Prism = require('./');

    Prism.plugins.autoloader.use_minified = !bool;
    debug = bool;
};

/**
 * Returns the debug extension based on whether debug is enabled.
 *
 * @returns {string}
 */
exports.getDebugExtension = function getExtension() {
    return !debug ? '.min' : '';
};
