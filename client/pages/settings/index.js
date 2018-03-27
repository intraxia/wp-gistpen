// @flow
// @jsx h
import '../../polyfills';
import { createStore, combineReducers } from 'redux';
import Kefir from 'kefir';
import { Aggregator, h } from 'brookjs-silt';
import ReactDOM from 'react-dom';
import { selectSettingsProps as selectProps } from '../../selectors';
import router from './router';
import { applyDelta, jobsDelta, routerDelta, siteDelta, webpackDelta } from '../../deltas';
import { ajaxReducer, globalsReducer, route, prism, gist, jobs, runs, messages } from '../../reducers';
import { ajax$ } from '../../services';
import { SettingsPage } from '../../components';

const { __GISTPEN_SETTINGS__ } = global;

const store = createStore(
    combineReducers({
        ajax: ajaxReducer,
        globals: globalsReducer,
        route,
        prism,
        gist,
        jobs,
        runs,
        messages
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
    <Aggregator action$={action$ => action$.observe(store.dispatch)}>
        <SettingsPage stream$={stream$} />
    </Aggregator>,
    // $FlowFixMe
    (document.getElementById('settings-app'): Element)
);
