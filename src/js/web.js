var Prism = require('./prism');
var Plite = require('plite');

/**
 * Configure Prism.
 */
Prism.plugins.autoloader.languages_path = Gistpen_Settings.url + 'assets/js/';
Prism.setDebug("1" === Gistpen_Settings.debug);

var promises = [];

promises.push(Prism.loadTheme(Gistpen_Settings.site.prism.theme));
promises.push(Prism.loadCSS('toolbar'));

if (Gistpen_Settings.site.prism['line-numbers']) {
    promises.push(Prism.loadPlugin('line-numbers'));
}

if (Gistpen_Settings.site.prism['show-invisibles']) {
    promises.push(Prism.loadPlugin('show-invisibles'));
}

window.PrismPromise = Plite.all(promises)
    .then(Prism.highlightAll)
    .catch(console.error.bind(console));
