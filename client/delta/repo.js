// @flow
import type { Action, Blob, EditorPageState, EditorInstance, RepoApiResponse } from '../type';
import type { Observable } from 'kefir';
import R from 'ramda';
import { ajax$ } from '../service';
import { EDITOR_UPDATE_CLICK, ajaxFailedAction, ajaxFinishedAction, repoSaveSucceededAction } from '../action';

const repoProps = R.pick(['description', 'status', 'password', 'sync']);
const blobProps = R.pick(['filename', 'code', 'language']);

const makeBody = (state : EditorPageState) : string => JSON.stringify({
    ...repoProps(state.editor),
    blobs: state.editor.instances.map((instance : EditorInstance) : Blob => {
        const blob = blobProps(instance);

        if (instance.key.indexOf('new') === -1) {
            blob.ID = parseInt(instance.key, 10);
        }

        return blob;
    })
});

const onlyEditorUpdateClicks = R.filter(R.pipe(
    R.prop('type'), R.equals(EDITOR_UPDATE_CLICK)
));

/**
 * Creates a new options delta stream for options API action.
 *
 * @param {Observable<T,U>} action$ - Stream of actions.
 * @param {Observable<T,U>} state$ - Stream of states.
 * @returns {Observable<T, U>} Options API stream.
 */
export default function repoDelta(action$ : Observable<Action>, state$ : Observable<EditorPageState>) : Observable<Action> {
    return state$.sampledBy(onlyEditorUpdateClicks(action$))
        .flatMapLatest((state : EditorPageState) : Observable<string> => ajax$(state.repo.rest_url, {
            method: 'PUT',
            body: makeBody(state),
            credentials: 'include',
            headers: {
                'X-WP-Nonce': state.globals.nonce,
                'Content-Type': 'application/json'
            }
        }))
        .map(JSON.parse)
        .flatten((response : RepoApiResponse) : Array<Action> => [ajaxFinishedAction(response), repoSaveSucceededAction(response)])
        .mapErrors(ajaxFailedAction);
}
