var Clipboard = require('clipboard');

/**
 * Post-highlight Prism hook callback.
 *
 * @param env
 */
exports.hook = function hook(env) {

    // Check if inline or actual code block (credit to line-numbers plugin)
    var pre = env.element.parentNode;
    if (!pre || !/pre/i.test(pre.nodeName)) {
        return;
    }

    pre.classList.add('code-toolbar');

    // Setup the toolbar
    var toolbar = document.createElement('div');
    toolbar.classList.add('toolbar');

    var linkCopy = document.createElement('a');
    linkCopy.innerHTML = 'Copy to clipboard';

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

    toolbar.appendChild(linkCopy);

    // Add our toolbar to the <pre> tag
    pre.appendChild(toolbar);

    function resetText() {
        setTimeout(function () {
            linkCopy.innerHTML = 'Copy to clipboard';
        }, 10000);
    }
};
