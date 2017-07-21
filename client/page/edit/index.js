// @flow
import type { Store, Reducer } from 'redux';
import type { Action, EditorPageState } from '../../type';
import '../../polyfills';
import { createStore, combineReducers } from 'redux';
import { domDelta } from 'brookjs';
import { applyDelta, repoDelta, commitsDelta, routerDelta, userDelta } from '../../delta';
import { globals, editor, repo, commits, route } from '../../reducer';
import { selectEditorProps as selectProps } from '../../selector';
import { ajax$ } from '../../service';
import { el, view } from './dom';
import router from './router';

const { __GISTPEN_EDITOR__ } = global;

// eslint-disable-next-line camelcase
__webpack_public_path__ = __GISTPEN_EDITOR__.globals.url + 'assets/js/';

const reducer : Reducer<EditorPageState, Action> = combineReducers({ globals, editor, commits, repo, route });

const store : Store<EditorPageState, Action> = createStore(
    reducer,
    __GISTPEN_EDITOR__,
    applyDelta(
        domDelta({ el, selectProps, view }),
        repoDelta,
        routerDelta({ router, param: 'wpgp_route' }),
        commitsDelta({ ajax$ }),
        userDelta
    )
);

store.dispatch({ type: 'INIT' });
