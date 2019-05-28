import '../../polyfills';
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

const initialState: State = {
  ajax: { running: false },
  authors: { items: {} },
  ...__GISTPEN_EDITOR__,
  editor: {
    ...__GISTPEN_EDITOR__.editor,
    instances:
      __GISTPEN_EDITOR__.editor.instances.length > 0
        ? __GISTPEN_EDITOR__.editor.instances
        : [
            {
              key: 'new0',
              filename: '',
              code: '\n',
              language: 'plaintext',
              cursor: false,
              history: {
                undo: [],
                redo: []
              }
            }
          ]
  }
};

const store = eddy()(createStore)(
  reducer,
  initialState,
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

document.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('edit-app');

  if (!el) {
    throw new Error('edit-app not found');
  }

  const App = connect(mapStateToProps)(View);

  ReactDOM.render(
    <Provider store={store}>
      <RootJunction root$={root$ => root$.observe(store.dispatch)}>
        <App />
      </RootJunction>
    </Provider>,
    el
  );

  // store.dispatch({ type: 'INIT' });
});
