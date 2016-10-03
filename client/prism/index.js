import Prism from 'prismjs/components/prism-core';
import 'prismjs/plugins/autoloader/prism-autoloader';
import toolbar from './toolbar';

// Prism highlights automatically by default.
document.removeEventListener('DOMContentLoaded', Prism.highlightAll);
Prism.hooks.add('complete', toolbar);

export default function prism(config) {
    // eslint-disable-next-line camelcase
    Prism.plugins.autoloader.languages_path = __webpack_public_path__;

    const promises = [];

    promises.push(System.import(
        `./themes/${config.prism.theme}.js`
    ));

    if (config.prism['line-numbers']) {
        promises.push(System.import(
            './plugins/line-numbers.js'
        ));
    }

    if (config.prism['show-invisibles']) {
        promises.push(System.import(
            './plugins/show-invisibles.js'
        ));
    }

    return Promise.all(promises).then(() => Prism);
}
