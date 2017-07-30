// @flow
import '../../polyfills';
import { createStore, combineReducers } from 'redux';
import { fromCallback } from 'kefir';
import { domDelta } from 'brookjs';
import view from './view';
import { selectSettingsProps as selectProps } from '../../selector';
import router from './router';
import { applyDelta, routerDelta, siteDelta, webpackDelta } from '../../delta';
import { globals, route, prism, gist, jobs, runs, messages } from '../../reducer';

const { __GISTPEN_SETTINGS__ } = global;

const el = (doc : Document) => fromCallback((callback : (value : null | HTMLElement) => void) =>
    callback(doc.querySelector('[data-brk-container="settings"]'))
);

createStore(
    combineReducers({ globals, route, prism, gist, jobs, runs, messages }),
    __GISTPEN_SETTINGS__,
    applyDelta(
        domDelta({ el, selectProps, view }),
        routerDelta({ router, param: 'wpgp_route' }),
        siteDelta,
        webpackDelta
    )
);
