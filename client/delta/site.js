import R from 'ramda';
import ajax$ from '../ajax';
import { ajaxFailedAction, ajaxFinishedAction } from '../action';

const makeBody = R.pipe(R.pick(['gist', 'prism']), JSON.stringify);
const optionsAjax$ = R.converge(ajax$, [
    state => state.globals.root + 'site',
    state => ({
        method: 'PATCH',
        body: makeBody(state),
        credentials: 'include',
        headers: {
            'X-WP-Nonce': state.globals.nonce,
            'Content-Type': 'application/json'
        }
    })
]);

/**
 * Creates a new options delta stream for options API action.
 *
 * @param {Observable<T,U>} action$ - Stream of actions.
 * @param {Observable<T,U>} state$ - Stream of states.
 * @returns {Observable<T, U>} Options API stream.
 */
export default function siteDelta(action$, state$) {
    return state$
        .slidingWindow(2, 2)
        .filter(([prev, next]) =>
            prev.gist !== next.gist || prev.prism !== next.prism)
        .debounce(1000)
        .map(R.last)
        .flatMapLatest(optionsAjax$)
        .map(R.pipe(JSON.parse, ajaxFinishedAction))
        .mapErrors(ajaxFailedAction);
}
