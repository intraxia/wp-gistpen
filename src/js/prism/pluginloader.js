var extend = require('lodash.assign');
var Plite = require('plite');
var debug = require('./debug');

/**
 * Load the plugin CSS.
 *
 * @param plugin
 * @returns {Promise}
 */
exports.loadCSS = function loadCSS(plugin) {
    var css = document.createElement("link");

    css.type = "text/css";
    css.rel = "stylesheet";
    css.href = Gistpen_Settings.url + 'assets/css/prism-' + plugin + this.getDebugExtension() + '.css';

    return makePromise(css, {footer: false})
};

/**
 * Load the plugin JS.
 *
 * @param plugin
 * @returns {Promise}
 */
exports.loadScript = function loadScript(plugin) {
    var js = document.createElement('script');
    js.async = true;
    js.src = Gistpen_Settings.url + 'assets/js/prism-' + plugin + this.getDebugExtension() + '.js';

    return makePromise(js, {footer: true})
};

/**
 * Load a plugin's scripts and styles.
 *
 * @param plugin
 * @returns {Promise} - Promise that resolved when script and style promises resolve.
 */
exports.loadPlugin = function loadPlugin(plugin) {
    return Plite.all([exports.loadScript(plugin), exports.loadCSS(plugin)]);
};

extend(exports, debug);

/**
 * Returns a promise that resolves on the loading of the elment.
 *
 * @param {Element} element
 */
function makePromise(element, opts) {
    return Plite(function(resolve, reject) {
        element.onload = resolve;
        element.onerror = reject;

        if (opts.footer) {
            document.body.appendChild(element);
        } else {
            document.getElementsByTagName("head")[0].appendChild(element);
        }
    });
}
