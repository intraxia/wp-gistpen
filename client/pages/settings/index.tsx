import '../../polyfills';
import { createStore } from 'redux';
import React from 'react';
import { RootJunction } from 'brookjs-silt';
import ReactDOM from 'react-dom';
import router from './router';
import {
  applyDelta,
  jobsDelta,
  routerDelta,
  siteDelta,
  webpackDelta
} from '../../deltas';
import { ajax$ } from '../../ajax';
import { SettingsPage } from '../../components';
import { reducer, State } from './state';
import mapStateToProps from './mapStateToProps';
import { connect, Provider } from 'react-redux';
import { eddy } from 'brookjs';
import { RootAction } from '../../util';

declare global {
  interface Window {
    __GISTPEN_SETTINGS__: State;
  }
}

const { __GISTPEN_SETTINGS__ } = window;
__webpack_public_path__ = __GISTPEN_SETTINGS__.globals.url + 'assets/js/';

const store = eddy()(createStore)(
  reducer,
  __GISTPEN_SETTINGS__,
  applyDelta<RootAction, State>(
    jobsDelta({ ajax$ }),
    routerDelta({
      router,
      param: 'wpgp_route',
      location: window.location,
      history: window.history
    }),
    siteDelta({ ajax$ }),
    webpackDelta
  )
);

const App = connect(mapStateToProps)(SettingsPage);

ReactDOM.render(
  <Provider store={store}>
    <RootJunction root$={root$ => root$.observe(store.dispatch)}>
      <App />
    </RootJunction>
  </Provider>,
  document.getElementById('settings-app')
);
