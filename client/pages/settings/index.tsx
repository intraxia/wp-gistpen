import { createStore } from 'redux';
import React from 'react';
import { RootJunction } from 'brookjs-silt';
import ReactDOM from 'react-dom';
import { connect, Provider } from 'react-redux';
import { eddy } from 'brookjs';
import {
  applyDelta,
  jobsDelta,
  routerDelta,
  siteDelta,
  webpackDelta,
} from '../../deltas';
import { ajax$ } from '../../ajax';
import { SettingsPage } from '../../components';
import Prism from '../../prism';
import { RootAction } from '../../util';
import { Job } from '../../reducers';
import mapStateToProps from './mapStateToProps';
import { reducer, State } from './state';
import router from './router';

type Omit<T, K extends keyof T> = Pick<T, Exclude<keyof T, K>>;

interface SettingsWindowState extends Omit<State, 'jobs'> {
  jobs: {
    [key: string]: Job;
  };
}

declare global {
  interface Window {
    __GISTPEN_SETTINGS__: SettingsWindowState;
  }
}

const { __GISTPEN_SETTINGS__ } = window;

Prism.setAutoloaderPath(
  (__webpack_public_path__ =
    __GISTPEN_SETTINGS__.globals.url + 'resources/assets/'),
);

const store = eddy()(createStore)(
  reducer,
  {
    ...__GISTPEN_SETTINGS__,
    jobs: Object.values(__GISTPEN_SETTINGS__.jobs).reduce(
      (jobs, job) => ({
        ...jobs,
        [job.slug]: { result: 'success', response: job },
      }),
      {},
    ),
  },
  applyDelta<RootAction, State>(
    jobsDelta({ ajax$ }),
    routerDelta({
      router,
      param: 'wpgp_route',
      location: window.location,
      history: window.history,
    }),
    siteDelta({ ajax$ }),
    webpackDelta,
  ),
);

const App = connect(mapStateToProps)(SettingsPage);

ReactDOM.render(
  <Provider store={store}>
    <RootJunction root$={root$ => root$.observe(store.dispatch)}>
      <App />
    </RootJunction>
  </Provider>,
  document.getElementById('settings-app'),
);
