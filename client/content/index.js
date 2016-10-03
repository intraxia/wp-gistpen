import Prism from '../prism';

if (!window.Promise) {
    require('es6-promise').polyfill();
}

const { __GISTPEN_CONTENT__ } = global;

// eslint-disable-next-line camelcase
__webpack_public_path__ = __GISTPEN_CONTENT__.url + 'assets/js/';

Prism(__GISTPEN_CONTENT__).then(prism => {
    if (document.readyState !== 'loading') {
        if (window.requestAnimationFrame) {
            window.requestAnimationFrame(prism.highlightAll);
        } else {
            window.setTimeout(prism.highlightAll, 16);
        }
    } else {
        document.addEventListener('DOMContentLoaded', prism.highlightAll);
    }
});
