// @flow
import '../../polyfills';
import { createStore, combineReducers } from 'redux';
import { fromESObservable } from 'kefir';
import { applyDelta, repoDelta, userDelta } from '../../delta';
import { api, editor, repo } from '../../reducer';
import component from './component';
import { selectEditorProps as selectProps } from '../../selector';

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
const state$ = selectProps(fromESObservable(store).toProperty(store.getState));

document.addEventListener('DOMContentLoaded', () => {
    const app$ = component(document.querySelector('[data-brk-container="editor"]'), state$);

    if (process.env.NODE_ENV !== 'production') {
        app$.spy('app$');
    }

    app$.observe({ value: store.dispatch });
});
