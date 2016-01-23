var Plite = require('plite');
var Prism = require('./compiled');

var css = document.createElement("link");
css.type = "text/css";
css.rel = "stylesheet";
document.getElementsByTagName("head")[0].appendChild(css);

module.exports = {
    highlightAll: highlightAll,
    loadTheme: loadTheme,
    loadPlugin: loadPlugin
};

function highlightAll() {
    Prism.highlightAll();
}

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
