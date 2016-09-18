var Clipboard = require('clipboard');

exports.button = function button(env) {
    var linkCopy = document.createElement('a');
    linkCopy.innerHTML = 'Copy';

    var clip = new Clipboard(linkCopy, {
        'text': function () {
            return env.code;
        }
    });

    clip.on('success', function() {
        linkCopy.innerHTML = 'Copied!';

        resetText();
    });
    clip.on('error', function () {
        linkCopy.innerHTML = 'Press Ctrl+C to copy';

        resetText();
    });

    return linkCopy;

    function resetText() {
        setTimeout(function () {
            linkCopy.innerHTML = 'Copy';
        }, 5000);
    }
};
