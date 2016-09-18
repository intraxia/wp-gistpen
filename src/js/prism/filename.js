exports.button = function(env) {
    var filename = document.createElement('span');
    var pre = env.element.parentElement;

    if (!pre.hasAttribute('data-filename')) {
        return;
    }

    filename.innerHTML = pre.getAttribute('data-filename');

    return filename;
};
