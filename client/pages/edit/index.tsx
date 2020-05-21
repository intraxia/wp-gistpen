import { createStore } from 'redux';
import { RootJunction, eddy } from 'brookjs';
import ReactDOM from 'react-dom';
import React from 'react';
import { Provider, connect } from 'react-redux';
import { ajax$ } from 'kefir-ajax';
import {
  applyDelta,
  authorDelta,
  repoDelta,
  commitsDelta,
  routerDelta,
  userDelta,
} from '../../deltas';
import { RootAction } from '../../util';
import { init } from '../../actions';
import { setAutoloaderPath } from '../../prism';
import router from './router';
import View from './View';
import { State, mapStateToProps, reducer } from './state';

interface EditWindowState extends State {}

declare global {
  interface Window {
    __GISTPEN_EDITOR__: EditWindowState;
  }
}

const { __GISTPEN_EDITOR__ } = window;

setAutoloaderPath(
  (__webpack_public_path__ =
    __GISTPEN_EDITOR__.globals.url + 'resources/assets/'),
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
      location: window.location,
    }),
    commitsDelta({ ajax$ }),
    userDelta({ ajax$ }),
  ),
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
    el,
  );

  store.dispatch(init(__GISTPEN_EDITOR__));
});
