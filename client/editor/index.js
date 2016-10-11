import { createStore, combineReducers } from 'redux';
import { fromESObservable } from 'kefir';
import root from './root';
import { applyDelta } from '../delta';
import { editor, globals, repo } from '../reducer';

const { __GISTPEN_EDITOR__ } = global;

// eslint-disable-next-line camelcase
__webpack_public_path__ = __GISTPEN_EDITOR__.globals.url + 'assets/js/';

const store = createStore(
    combineReducers({ editor, globals, repo }),
    __GISTPEN_EDITOR__,
    applyDelta()
);
const state$ = fromESObservable(store).toProperty(store.getState);

document.addEventListener('DOMContentLoaded', () => {
    const app$ = root(document.querySelector('[data-brk-container="editor"]'), state$);

    if (process.env.NODE_ENV !== 'production') {
        app$.log('app$');
    }

    app$.observe({ value: store.dispatch });
});
