import { createStore } from 'redux';
import { RootJunction } from 'brookjs-silt';
import ReactDOM from 'react-dom';
import {
  applyDelta,
  authorDelta,
  repoDelta,
  commitsDelta,
  routerDelta,
  userDelta
} from '../../deltas';
import { ajax$ } from '../../ajax';
import router from './router';
import { RootAction } from '../../util';
import React from 'react';
import { Provider, connect } from 'react-redux';
import View from './View';
import { State, mapStateToProps, reducer } from './state';
import { eddy } from 'brookjs';
import Prism from '../../prism';
import { init } from '../../actions';

interface EditWindowState extends State {}

declare global {
  interface Window {
    __GISTPEN_EDITOR__: EditWindowState;
  }
}

const { __GISTPEN_EDITOR__ } = window;

// eslint-disable-next-line camelcase
Prism.setAutoloaderPath(
  (__webpack_public_path__ = __GISTPEN_EDITOR__.globals.url + 'assets/js/')
);

const store = eddy()(createStore)(
  reducer,
  __GISTPEN_EDITOR__,
  applyDelta<RootAction, State>(
    authorDelta({ ajax$ }),
    repoDelta({ ajax$ }),
    routerDelta({
      router,
      param: 'wpgp_route',
      history: window.history,
      location: window.location
    }),
    commitsDelta({ ajax$ }),
    userDelta({ ajax$ })
  )
);

const App = connect(mapStateToProps)(View);

document.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('edit-app');

  if (el == null) {
    throw new Error('edit-app not found');
  }

  ReactDOM.render(
    <Provider store={store}>
      <RootJunction root$={root$ => root$.observe(store.dispatch)}>
        <App />
      </RootJunction>
    </Provider>,
    el
  );

  store.dispatch(init(__GISTPEN_EDITOR__));
});
