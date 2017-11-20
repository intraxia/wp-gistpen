// @flow
import type { Store, Reducer } from 'redux';
import type { Action, EditorPageState } from '../../type';
import '../../polyfills';
import { createStore, combineReducers } from 'redux';
import { domDelta } from 'brookjs';
import { applyDelta, authorDelta, repoDelta, commitsDelta, routerDelta, userDelta } from '../../delta';
import { authors, globals, editor, repo, commits, route } from '../../reducer';
import { selectEditorProps as selectProps } from '../../selector';
import { ajax$ } from '../../service';
import { el, view } from './dom';
import router from './router';

const { __GISTPEN_EDITOR__ } = global;

// eslint-disable-next-line camelcase
__webpack_public_path__ = __GISTPEN_EDITOR__.globals.url + 'assets/js/';

const reducer : Reducer<EditorPageState, Action> = combineReducers({ authors, globals, editor, commits, repo, route });

const initialState = {
    ...__GISTPEN_EDITOR__,
    editor: {
        ...__GISTPEN_EDITOR__.editor,
        instances: __GISTPEN_EDITOR__.editor.instances.length > 0 ? __GISTPEN_EDITOR__.editor.instances : [{
            key: 'new0',
            filename: '',
            code: '\n',
            language: 'plaintext',
            cursor: false,
            history: {
                undo: [],
                redo: []
            }
        }]
    }
};

const store : Store<EditorPageState, Action> = createStore(
    reducer,
    initialState,
    applyDelta(
        authorDelta({ ajax$ }),
        domDelta({ el, selectProps, view }),
        repoDelta,
        routerDelta({ router, param: 'wpgp_route' }),
        commitsDelta({ ajax$ }),
        userDelta
    )
);

store.dispatch({ type: 'INIT' });
