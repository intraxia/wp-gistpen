var Prism = require('./prism');
var Plite = require('plite');
var toolbar = require('./prism/toolbar');
var clipboard =require('./prism/clipboard');
var forOwn = require('lodash.forown');

var promises = [];

promises.push(Prism.loadTheme(Gistpen_Settings.prism.theme));
promises.push(Prism.loadPlugin('toolbar'));

forOwn(Gistpen_Settings.prism.plugins, function(props, plugin) {
    if (props.enabled) {
        promises.push(Prism.loadPlugin(plugin));

        if ('show-language' === plugin) {
            toolbar.registerButton(function(env) {
                var langCopy = document.createElement('span');
                langCopy.innerHTML = env.language;
                return langCopy;
            });
        }
    }
});

toolbar.registerButton(function(env) {
    var pre = env.element.parentElement;

    if (!pre.hasAttribute('data-edit-url')) {
        return;
    }

    var url = pre.getAttribute('data-edit-url');

    var editBtn = document.createElement('a');
    editBtn.innerHTML = 'Edit';
    editBtn.href = url;
    editBtn.setAttribute('target', '_blank');

    return editBtn;
});

toolbar.registerButton(clipboard.button);

Prism.hooks.add('after-highlight', toolbar.hook);

Plite.all(promises)
    .then(Prism.highlightAll)
    .catch(function(err) {
        console.error(err);
    });
