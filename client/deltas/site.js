// @flow
import type { Observable } from 'kefir';
import type { Action, SettingsState } from '../types';
import type { AjaxOptions, ObsResponse } from '../ajax';
import R from 'ramda';
import Kefir from 'kefir';
import { ajax$ } from '../ajax';
import { ajaxStarted, ajaxFailed, ajaxFinished } from '../actions';

const optionsAjax$ : (state: SettingsState) => Observable<ObsResponse> = R.converge(ajax$, [
    (state: SettingsState): string => state.globals.root + 'site',
    (state: SettingsState): AjaxOptions => ({
        method: 'PATCH',
        body: JSON.stringify({
            gist: state.gist,
            prism: state.prism
        }),
        credentials: 'include',
        headers: {
            'X-WP-Nonce': state.globals.nonce,
            'Content-Type': 'application/json'
        }
    })
]);

/**
 * Creates a new options delta stream for options API actions.
 *
 * @param {Observable<T,U>} action$ - Stream of actions.
 * @param {Observable<T,U>} state$ - Stream of states.
 * @returns {Observable<T, U>} Options API stream.
 */
export default function siteDelta(action$: Observable<Action>, state$: Observable<SettingsState>): Observable<Action> {
    return state$
        .skip(1)
        .skipDuplicates(
            (prev: SettingsState, next: SettingsState): boolean =>
                prev.gist === next.gist && prev.prism === next.prism)
        .debounce(1000)
        .flatMapLatest(state => Kefir.concat([
            Kefir.constant(ajaxStarted()),
            optionsAjax$(state)
                .flatMap((response: ObsResponse) => response.json())
                .map(ajaxFinished)
                .mapErrors(ajaxFailed)
        ]));
}
