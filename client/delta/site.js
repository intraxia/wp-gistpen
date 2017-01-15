// @flow
import type { Observable } from 'kefir';
import type { Action, SettingsState, AjaxOptions, } from '../type';
import R from 'ramda';
import ajax$ from '../ajax';
import { ajaxFailedAction, ajaxFinishedAction } from '../action';

const makeBody = R.pipe(R.pick(['gist', 'prism']), JSON.stringify);
const optionsAjax$ : (state : SettingsState) => Observable<Action> = R.converge(ajax$, [
    (state : SettingsState) : string => state.const.root + 'site',
    (state : SettingsState) : AjaxOptions => ({
        method: 'PATCH',
        body: makeBody(state),
        credentials: 'include',
        headers: {
            'X-WP-Nonce': state.const.nonce,
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
export default function siteDelta(action$ : Observable<Action>, state$ : Observable<SettingsState>) : Observable<Action> {
    return state$
        .skipDuplicates(
            (prev : SettingsState, next : SettingsState) : boolean =>
                prev.gist === next.gist && prev.prism === next.prism)
        .debounce(1000)
        .flatMapLatest(optionsAjax$)
        .map(R.pipe(JSON.parse, ajaxFinishedAction))
        .mapErrors(ajaxFailedAction);
}
