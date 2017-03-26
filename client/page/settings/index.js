// @flow
import '../../polyfills';
import { createStore, combineReducers } from 'redux';
import { fromESObservable } from 'kefir';
import component from './component';
import { selectSettingsProps as selectProps } from '../../selector';
import router from './router';
import { applyDelta, routerDelta, siteDelta, webpackDelta } from '../../delta';
import { globals, route, prism, gist } from '../../reducer';

const { __GISTPEN_SETTINGS__ } = global;

const store = createStore(
    combineReducers({ globals, route, prism, gist }),
    __GISTPEN_SETTINGS__,
    applyDelta(
        routerDelta({ router }),
        siteDelta,
        webpackDelta
    )
);
const props$ = selectProps(fromESObservable(store).toProperty(store.getState));

document.addEventListener('DOMContentLoaded', () => {
    const app$ = component(document.querySelector('[data-brk-container="settings"]'), props$);

    if (process.env.NODE_ENV !== 'production') {
        app$.log('app$');
    }

    app$.observe({ value: store.dispatch });
});
