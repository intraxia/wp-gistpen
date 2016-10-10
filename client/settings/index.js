import R from 'ramda';
import { createStore, combineReducers } from 'redux';
import { fromESObservable } from 'kefir';
import root from './root';
import router from './router';
import { applyDelta, createRouterDelta, siteDelta } from '../delta';
import { globals, route, prism, gist } from '../reducer';

const { __GISTPEN_SETTINGS__ } = global;

// eslint-disable-next-line camelcase
__webpack_public_path__ = __GISTPEN_SETTINGS__.const.url + 'assets/js/';

let enhancer = applyDelta(createRouterDelta(router), siteDelta);

let reducer = combineReducers({
    globals,
    route,
    prism,
    gist
});

if (process.env.NODE_ENV !== 'production') {
    // To use devtools, install Chrome extension:
    // https://chrome.google.com/webstore/detail/redux-devtools/lmhkpmbekcpmknklioeibfkpmmfibljd
    enhancer = R.pipe(global.devToolsExtension ? global.devToolsExtension() : R.identity, enhancer);
}

const store = createStore(reducer, __GISTPEN_SETTINGS__, enhancer);
const state$ = fromESObservable(store).toProperty(store.getState);

document.addEventListener('DOMContentLoaded', () => {
    const app$ = root(document.querySelector('[data-brk-container="settings"]'), state$);

    if (process.env.NODE_ENV !== 'production') {
        app$.log('app$');
    }

    app$.observe({ value: store.dispatch });
});
