var Prism = require('./prism');
var Plite = require('plite');
var toolbar = require('./prism/toolbar');
var clipboard =require('./prism/clipboard');
var forOwn = require('lodash.forown');

var secret, secretTimeout;

var promises = [];

promises.push(Prism.loadTheme(Gistpen_Settings.prism.theme));
promises.push(Prism.loadPlugin('toolbar'));

toolbar.registerButton(function(env) {
    var pre = env.element.parentElement;

    if (!pre.hasAttribute('data-edit-url')) {
        return;
    }

    var url = pre.getAttribute('data-edit-url');

    var editBtn = document.createElement('a');
    editBtn.innerHTML = 'Edit';

    editBtn.addEventListener('click', function () {
        sendEmbedMessage('link', url);
    });

    return editBtn;
});

toolbar.registerButton(clipboard.button);

forOwn(Gistpen_Settings.prism.plugins, function(props, plugin) {
    if (props.enabled) {
        promises.push(Prism.loadPlugin(plugin));
    }
});

toolbar.registerButton(function(env) {
    var filename = document.createElement('span');
    var pre = env.element.parentElement;

    if (!pre.hasAttribute('data-filename')) {
        return;
    }

    filename.innerHTML = pre.getAttribute('data-filename');

    return filename;
});

Prism.hooks.add('after-highlight', toolbar.hook);

getSecret();

Plite.all(promises)
    .then(Prism.highlightAll)
    .catch(function(err) {
        console.error(err);
    });

/**
 * WordPress-borrowed functions.
 *
 * @todo Is there a better way to hook into this?
 * The link tag doesn't exist on page load for us,
 * so the WP oembed script doesn't grab it and add
 * the click handler for us. We're simply copying
 * functions for reuse.
 */

/**
 * Sends a formatted message to the parent window.
 *
 * @param {string} message - Message name.
 * @param {mixed} value - Data to send.
 */
function sendEmbedMessage(message, value) {
    window.parent.postMessage({
        message: message,
        value: value,
        secret: secret
    }, '*');
}

/**
 * Re-get the secret when it was added later on.
 */
function getSecret() {
    if (window.self === window.top || !!secret) {
        return;
    }

    secret = window.location.hash.replace(/.*secret=([\d\w]{10}).*/, '$1');

    clearTimeout(secretTimeout);

    secretTimeout = setTimeout(function () {
        getSecret();
    }, 100);
}