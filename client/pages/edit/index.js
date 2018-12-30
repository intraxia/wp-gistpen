// @flow
// @jsx h
import type { Store, Reducer } from 'redux';
import type { Action, EditorPageState } from '../../types';
import '../../polyfills';
import { createStore, combineReducers } from 'redux';
import { h, view, RootJunction } from 'brookjs-silt';
import Kefir from 'kefir';
import ReactDOM from 'react-dom';
import { applyDelta, authorDelta, repoDelta, commitsDelta, routerDelta, userDelta } from '../../deltas';
import { ajaxReducer, authorsReducer, globalsReducer, editorReducer, repoReducer, commitsReducer, routeReducer } from '../../reducers';
import { selectEditorProps as selectProps } from '../../selectors';
import { ajax$ } from '../../services';
import { Editor, Commits } from '../../components';
import router from './router';

const { __GISTPEN_EDITOR__ } = global;

// eslint-disable-next-line camelcase
__webpack_public_path__ = __GISTPEN_EDITOR__.globals.url + 'assets/js/';

const reducer : Reducer<EditorPageState, Action> = combineReducers({
    ajax: ajaxReducer,
    authors: authorsReducer,
    globals: globalsReducer,
    editor: editorReducer,
    commits: commitsReducer,
    repo: repoReducer,
    route: routeReducer
});

const initialState: EditorPageState = {
    ajax: { running: false },
    authors: { items: {} },
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

const store : Store<EditorPageState, Action> = createStore( // eslint-disable-line no-unused-vars
    reducer,
    initialState,
    applyDelta(
        authorDelta({ ajax$ }),
        repoDelta,
        routerDelta({ router, param: 'wpgp_route' }),
        commitsDelta({ ajax$ }),
        userDelta
    )
);

const stream$ = Kefir.fromESObservable(store).toProperty(store.getState).thru(selectProps);

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('edit-app');

    if (!el) {
        throw new Error('edit-app not found');
    }

    ReactDOM.render(
        <RootJunction silt-embeddable root$={root$ => root$.observe(store.dispatch)}>
            {stream$.thru(view(props => props.route.name)).map(route => {
                switch (route) {
                    case 'editor':
                        return <Editor stream$={stream$} />;
                    case 'commits':
                        return <Commits stream$={stream$} />;
                    default:
                        return null;
                }
            })}
        </RootJunction>,
        el
    );
});

// store.dispatch({ type: 'INIT' });
