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
        if ('show-invisibles' === plugin) {
            showInvisibles = true;
        }
    }
});

Plite.all(promises)
    .then(function() {
        if (showInvisibles) {
            // @todo https://github.com/PrismJS/prism/pull/874
            Prism.hooks.add('before-highlight', function(env) {
                var tokens = env.grammar;

                tokens.tab = /\t/g;
                tokens.crlf = /\r\n/g;
                tokens.lf = /\n/g;
                tokens.cr = /\r/g;
            });
        }

        Prism.highlightAll();
    })
    .catch(function(err) {
        console.error(err);
    });
