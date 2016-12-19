import R from 'ramda';
import ajax$ from '../ajax';
import { EDITOR_UPDATE_CLICK, ajaxFailedAction, ajaxFinishedAction } from '../action';

const repoProps = R.pick(['description', 'status', 'password', 'sync']);
const blobProps = R.pick(['filename', 'code', 'language']);

const makeBody = state => JSON.stringify({
    ...repoProps(state.editor),
    blobs: state.editor.instances.map(instance => {
        const blob = blobProps(instance);

        if (instance.key.indexOf('new') === -1) {
            blob.ID = instance.key;
        }

        return blob;
    })
});

const onlyUpdateClicks = R.filter(R.pipe(
    R.prop('type'), R.equals(EDITOR_UPDATE_CLICK)
));

/**
 * Creates a new options delta stream for options API action.
 *
 * @param {Observable<T,U>} action$ - Stream of actions.
 * @param {Observable<T,U>} state$ - Stream of states.
 * @returns {Observable<T, U>} Options API stream.
 */
export default function siteDelta(action$, state$) {
    return state$.sampledBy(onlyUpdateClicks(action$))
        .flatMapLatest(state => ajax$(state.repo.rest_url, {
            method: 'PUT',
            body: makeBody(state),
            credentials: 'include',
            headers: {
                'X-WP-Nonce': state.api.nonce,
                'Content-Type': 'application/json'
            }
        }))
        .map(R.pipe(JSON.parse, ajaxFinishedAction))
        .mapErrors(ajaxFailedAction);;
}
