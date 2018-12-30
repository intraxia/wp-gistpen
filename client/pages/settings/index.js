// @flow
// @jsx h
import '../../polyfills';
import type { Action, SettingsState } from '../../types';
import { createStore, combineReducers, type Store } from 'redux';
import Kefir from 'kefir';
import { RootJunction, h } from 'brookjs-silt';
import ReactDOM from 'react-dom';
import { selectSettingsProps as selectProps } from '../../selectors';
import router from './router';
import { applyDelta, jobsDelta, routerDelta, siteDelta, webpackDelta } from '../../deltas';
import { ajaxReducer, globalsReducer, routeReducer, prismReducer, gistReducer, jobsReducer, runsReducer, messagesReducer } from '../../reducers';
import { ajax$ } from '../../services';
import { SettingsPage } from '../../components';

const { __GISTPEN_SETTINGS__ } = global;

const store: Store<SettingsState, Action> = createStore(
    combineReducers({
        ajax: ajaxReducer,
        globals: globalsReducer,
        route: routeReducer,
        prism: prismReducer,
        gist: gistReducer,
        jobs: jobsReducer,
        runs: runsReducer,
        messages: messagesReducer
    }),
    __GISTPEN_SETTINGS__,
    applyDelta(
        jobsDelta({ ajax$ }),
        routerDelta({ router, param: 'wpgp_route' }),
        siteDelta,
        webpackDelta
    )
);

// $FlowFixMe
const stream$ = Kefir.fromESObservable(store).toProperty(store.getState).map(selectProps);

ReactDOM.render(
    <RootJunction action$={action$ => action$.observe(store.dispatch)}>
        <SettingsPage stream$={stream$} />
    </RootJunction>,
    // $FlowFixMe
    (document.getElementById('settings-app'): Element)
);
