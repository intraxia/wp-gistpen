// @flow
import '../../polyfills';
import { createStore, combineReducers } from 'redux';
import { fromCallback } from 'kefir';
import { domDelta } from 'brookjs';
import { applyDelta, repoDelta, userDelta } from '../../delta';
import { api, editor, repo } from '../../reducer';
import view from './view';
import { selectEditorProps as selectProps } from '../../selector';

const { __GISTPEN_EDITOR__ } = global;

// eslint-disable-next-line camelcase
__webpack_public_path__ = __GISTPEN_EDITOR__.api.url + 'assets/js/';

const el = (doc : Document) => fromCallback((callback : (value : null | HTMLElement) => void) => {
    callback(doc.querySelector('[data-brk-container="editor"]'));
});

createStore(
    combineReducers({ api, editor, repo }),
    __GISTPEN_EDITOR__,
    applyDelta(
        domDelta({ el, selectProps, view }),
        repoDelta,
        userDelta
    )
);
