// @flow
import '../../polyfills';
import { createStore, combineReducers } from 'redux';
import { fromCallback } from 'kefir';
import { domDelta, containerAttribute } from 'brookjs';
import view from './view';
import { selectSettingsProps as selectProps } from '../../selectors';
import router from './router';
import { applyDelta, jobsDelta, routerDelta, siteDelta, webpackDelta } from '../../deltas';
import { globals, route, prism, gist, jobs, runs, messages } from '../../reducers';
import { ajax$ } from '../../services';

const { __GISTPEN_SETTINGS__ } = global;

const el = (doc : Document) => fromCallback((callback : (value : null | HTMLElement) => void) =>
    callback(doc.querySelector(`[${containerAttribute('settings')}]`))
);

createStore(
    combineReducers({ globals, route, prism, gist, jobs, runs, messages }),
    __GISTPEN_SETTINGS__,
    applyDelta(
        domDelta({ el, selectProps, view }),
        jobsDelta({ ajax$ }),
        routerDelta({ router, param: 'wpgp_route' }),
        siteDelta,
        webpackDelta
    )
);
