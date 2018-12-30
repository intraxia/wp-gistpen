// @flow
import type { Action, EditorPageState, UserApiResponse } from '../types';
import type { Observable } from 'kefir';
import type { ObsResponse } from '../services';
import { ofType } from 'brookjs';
import { ajax$ } from '../services';
import { ajaxFinished, ajaxFailed, userSaveSucceeded, editorWidthChange,
    editorInvisiblesToggle, editorTabsToggle, editorThemeChange } from '../actions';
import { selectUserAjaxOpts } from '../selectors';

/**
 * User endpoint delta.
 *
 * @param {Observable<Action>} actions$ - Stream of store actions.
 * @param {Observable<EditorPageState>} state$ - Stream of store states.
 * @returns {Observable<Action>} Stream of actions.
 */
export default function userDelta(actions$: Observable<Action>, state$: Observable<EditorPageState>): Observable<Action> {
    return state$.sampledBy(actions$.thru(ofType(
        editorWidthChange,
        editorInvisiblesToggle,
        editorTabsToggle,
        editorThemeChange
    )))
        .debounce(2500)
        .flatMapLatest((state: EditorPageState): Observable<ObsResponse> => ajax$(state.globals.root + 'me', selectUserAjaxOpts(state)))
        .flatMap((response: ObsResponse) => response.json())
        .flatten((response: UserApiResponse): Array<Action> => [ajaxFinished(response), userSaveSucceeded(response)])
        .mapErrors(ajaxFailed);
}
