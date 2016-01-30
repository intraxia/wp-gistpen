var Prism = require('./prism');
var Plite = require('plite');
var forOwn = require('lodash.forown');

/**
 * Configure Prism.
 */
Prism.plugins.autoloader.languages_path = Gistpen_Settings.url + 'assets/js/';
Prism.setDebug("1" === Gistpen_Settings.debug);

var showInvisibles = false;
var promises = [];

promises.push(Prism.loadTheme(Gistpen_Settings.prism.theme));

forOwn(Gistpen_Settings.prism.plugins, function(props, plugin) {
    if (props.enabled) {
        promises.push(Prism.loadPlugin(plugin));
    }
});

Plite.all(promises)
    .then(Prism.highlightAll)
    .catch(function(err) {
        console.error(err);
    });
