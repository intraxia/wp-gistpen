var secret;
var secretTimeout;

getSecret();

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

/**
 * Registers the edit button with the toolbar.
 *
 * @param env
 * @returns {Element}
 */
exports.button = function(env) {
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
};

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
