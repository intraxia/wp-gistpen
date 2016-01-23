var Prism = require('./prism');
var Plite = require('plite');

var promises = [];
promises.push(Prism.loadTheme(Gistpen_Settings.prism.theme));

// This should loop over all the prism plugins in the settings.
if (Gistpen_Settings.prism.plugins['line-numbers'].enabled) {
    promises.push(Prism.loadPlugin('line-numbers'));
}

Plite.all(promises)
    .then(Prism.highlightAll)
    .catch(function(err) {
        console.error(err);
    });
