// @flow
import type { Observable } from 'kefir';
import type { Action, SettingsState } from '../type';
import type { AjaxOptions, ObsResponse } from '../service';
import R from 'ramda';
import { ajax$ } from '../service';
import { ajaxFailedAction, ajaxFinishedAction } from '../action';

const makeBody = R.pipe(R.pick(['gist', 'prism']), JSON.stringify);
const optionsAjax$ : (state : SettingsState) => Observable<ObsResponse> = R.converge(ajax$, [
    (state : SettingsState) : string => state.globals.root + 'site',
    (state : SettingsState) : AjaxOptions => ({
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
export default function siteDelta(action$ : Observable<Action>, state$ : Observable<SettingsState>) : Observable<Action> {
    return state$
        .skip(1)
        .skipDuplicates(
            (prev : SettingsState, next : SettingsState) : boolean =>
                prev.gist === next.gist && prev.prism === next.prism)
        .debounce(1000)
        .flatMapLatest(optionsAjax$)
        .flatMap((response : ObsResponse) => response.json())
        .map(ajaxFinishedAction)
        .mapErrors(ajaxFailedAction);
}
