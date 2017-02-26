import '../polyfills';
import { createStore, combineReducers } from 'redux';
import { fromESObservable } from 'kefir';
import root from './root';
import router from './router';
import { applyDelta, createRouterDelta, siteDelta, webpackDelta } from '../delta';
import { globals, route, prism, gist } from '../reducer';

const { __GISTPEN_SETTINGS__ } = global;

const store = createStore(
    combineReducers({ globals, route, prism, gist }),
    __GISTPEN_SETTINGS__,
    applyDelta(
        createRouterDelta(router),
        siteDelta,
        webpackDelta
    )
);
const state$ = fromESObservable(store).toProperty(store.getState);

document.addEventListener('DOMContentLoaded', () => {
    const app$ = root(document.querySelector('[data-brk-container="settings"]'), state$);

    if (process.env.NODE_ENV !== 'production') {
        app$.log('app$');
    }

    app$.observe({ value: store.dispatch });
});
