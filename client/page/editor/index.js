import '../../polyfills';
import { createStore, combineReducers } from 'redux';
import { fromESObservable } from 'kefir';
import root from './root';
import { applyDelta, repoDelta, userDelta } from '../../delta';
import { api, editor, repo } from '../../reducer';

const { __GISTPEN_EDITOR__ } = global;

// eslint-disable-next-line camelcase
__webpack_public_path__ = __GISTPEN_EDITOR__.api.url + 'assets/js/';

const store = createStore(
    combineReducers({ api, editor, repo }),
    __GISTPEN_EDITOR__,
    applyDelta(
        repoDelta,
        userDelta
    )
);
const state$ = fromESObservable(store).toProperty(store.getState);

document.addEventListener('DOMContentLoaded', () => {
    const app$ = root(document.querySelector('[data-brk-container="editor"]'), state$);

    if (process.env.NODE_ENV !== 'production') {
        app$.spy('app$');
    }

    app$.observe({ value: store.dispatch });
});
