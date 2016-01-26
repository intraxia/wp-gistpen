var callbacks = [];

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

    callbacks.forEach(function(callback) {
        var element = callback(env);

        if (!element) {
            return;
        }

        var item = document.createElement('div');
        item.classList.add('toolbar-item');

        item.appendChild(element);
        toolbar.appendChild(item);
    });

    // Add our toolbar to the <pre> tag
    pre.appendChild(toolbar);
};

/**
 * Register a button callback with the toolbar.
 *
 * @param callback
 */
exports.registerButton = function registerButton(callback) {
    callbacks.push(callback);
};
