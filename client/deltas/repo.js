// @flow
import type { Action, EditorPageState, EditorInstance, RepoApiResponse, EditorState } from '../types';
import type { Observable } from 'kefir';
import type { ObsResponse } from '../services';
import Kefir from 'kefir';
import { ofType } from 'brookjs';
import { ajax$ } from '../ajax';
import { editorUpdateClick, ajaxStarted, ajaxFailed, ajaxFinished, repoSaveSucceeded } from '../actions';

type ApiRequestBlob = {
    ID?: number,
    filename: string,
    code: string,
    language: string
};

const repoProps = (editor: EditorState) => ({
    description: editor.description,
    status: editor.status,
    password: editor.password,
    sync: editor.sync
});

const blobProps = (editor: EditorInstance): ApiRequestBlob => ({
    filename: editor.filename,
    code: editor.code,
    language: editor.language
});

const makeBody = (state: EditorPageState): string => JSON.stringify({
    ...repoProps(state.editor),
    blobs: state.editor.instances.map((instance: EditorInstance): ApiRequestBlob => {
        const blob = blobProps(instance);

        if (instance.key.indexOf('new') === -1) {
            blob.ID = parseInt(instance.key, 10);
        }

        return blob;
    })
});

/**
 * Creates a new options delta stream for options API actions.
 *
 * @param {Observable<T,U>} action$ - Stream of actions.
 * @param {Observable<T,U>} state$ - Stream of states.
 * @returns {Observable<T, U>} Options API stream.
 */
export default function repoDelta(action$: Observable<Action>, state$: Observable<EditorPageState>): Observable<Action> {
    return state$.sampledBy(action$.thru(ofType(editorUpdateClick)))
        .flatMapLatest((state: EditorPageState): Observable<Action> => Kefir.concat([
            Kefir.constant(ajaxStarted()),
            ajax$(state.repo.rest_url, {
                method: 'PUT',
                body: makeBody(state),
                credentials: 'include',
                headers: {
                    'X-WP-Nonce': state.globals.nonce,
                    'Content-Type': 'application/json'
                }
            })
                .flatMap((response: ObsResponse) => response.json())
                .flatten((response: RepoApiResponse): Array<Action> => [ajaxFinished(response), repoSaveSucceeded(response)])
                .flatMapErrors(err => Kefir.constant(ajaxFailed(err)))
        ]));
}
