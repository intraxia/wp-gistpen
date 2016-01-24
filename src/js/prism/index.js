var Plite = require('plite');
var Prism = require('./compiled');
var extend = require('lodash.assign');

var css = document.createElement("link");
css.type = "text/css";
css.rel = "stylesheet";
document.getElementsByTagName("head")[0].appendChild(css);

var api = {
    loadTheme: loadTheme,
    loadPlugin: loadPlugin
};

extend(api, Prism);
module.exports = api;


function loadTheme(theme) {
    return Plite(function(resolve, reject) {
        theme = theme === 'default' ? '' : '-' + theme;

        css.onload = resolve;
        css.onerror = reject;

        css.href = Gistpen_Settings.url + 'assets/css/prism' + theme + '.css';
    });
}

function loadPlugin(plugin) {
    return Plite(function(resolve, reject) {
        var css = document.createElement("link");
        css.type = "text/css";
        css.rel = "stylesheet";

        css.onload = resolve;
        css.onerror = reject;

        css.href = Gistpen_Settings.url + 'assets/css/prism-' + plugin + '.css';
        document.getElementsByTagName("head")[0].appendChild(css);
    });
}
