var extend = require('lodash.assign');
var Plite = require('plite');
var debug = require('./debug');

var css = document.createElement("link");
css.type = "text/css";
css.rel = "stylesheet";
document.getElementsByTagName("head")[0].appendChild(css);

/**
 * Load the theme CSS.
 *
 * @param theme
 * @returns {Promise}
 */
exports.loadTheme = function loadTheme(theme) {
    return Plite(function(resolve, reject) {
        if (!theme) {
            theme = 'default';
        }

        theme = theme === 'default' ? '' : '-' + theme;

        css.onload = resolve;
        css.onerror = reject;

        css.href = Gistpen_Settings.url + 'assets/css/prism' + theme + this.getDebugExtension() + '.css';
    }.bind(this));
};

extend(exports, debug);
