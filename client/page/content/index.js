// @flow
import '../../polyfills';
import Prism from '../../prism';

import { plugin } from '../../prism/plugins/toolbar';
import '../../prism/plugins/copy-to-clipboard';
import '../../prism/plugins/edit';
import '../../prism/plugins/filename';

plugin.use();

const { __GISTPEN_CONTENT__ } = global;

// eslint-disable-next-line camelcase
Prism.setAutoloaderPath(__webpack_public_path__ = __GISTPEN_CONTENT__.globals.url + 'assets/js/');

const promises : Array<Promise<void>> = [];

promises.push(Prism.setTheme(__GISTPEN_CONTENT__.prism.theme));

if (__GISTPEN_CONTENT__.prism['line-numbers']) {
    promises.push(Prism.togglePlugin('line-numbers', true));
}

if (__GISTPEN_CONTENT__.prism['show-invisibles']) {
    promises.push(Prism.togglePlugin('show-invisibles', true));
}

Promise.all(promises).then(() => {
    if (document.readyState !== 'loading') {
        if (window.requestAnimationFrame) {
            window.requestAnimationFrame(Prism.highlightAll);
        } else {
            window.setTimeout(Prism.highlightAll, 16);
        }
    } else {
        document.addEventListener('DOMContentLoaded', Prism.highlightAll);
    }
});
