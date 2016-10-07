import R from 'ramda';
import { applyMiddleware, createStore } from 'redux';
import { fromESObservable } from 'kefir';
import { observeDelta } from 'brookjs';
import root from './root';
import router from './router';
import { createRouterDelta, siteDelta } from '../delta';
import reducer from '../reducer';

const { __GISTPEN_SETTINGS__ } = global;

let enhancer = applyMiddleware(observeDelta(createRouterDelta(router), siteDelta));

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
