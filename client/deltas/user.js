// @flow
import type { Action, EditorPageState, UserApiResponse } from '../types';
import type { Observable } from 'kefir';
import type { ObsResponse } from '../services';
import R from 'ramda';
import { merge } from 'kefir';
import { ajax$ } from '../services';
import { ajaxFinishedAction, ajaxFailedAction, userSaveSucceededAction, EDITOR_WIDTH_CHANGE,
    EDITOR_INVISIBLES_TOGGLE, EDITOR_TABS_TOGGLE, EDITOR_THEME_CHANGE } from '../actions';
import { selectUserAjaxOpts } from '../selectors';

/**
 * User endpoint delta.
 *
 * @param {Observable<Action>} actions$ - Stream of store actions.
 * @param {Observable<EditorPageState>} state$ - Stream of store states.
 * @returns {Observable<Action>} Stream of actions.
 */
export default function userDelta(actions$: Observable<Action>, state$: Observable<EditorPageState>): Observable<Action> {
    const editorWidthChange$ = actions$.filter(
        R.propEq('type', EDITOR_WIDTH_CHANGE)
    );

    const editorInvisiblesToggle$ = actions$.filter(
        R.propEq('type', EDITOR_INVISIBLES_TOGGLE)
    );

    const editorTabsToggle$ = actions$.filter(
        R.propEq('type', EDITOR_TABS_TOGGLE)
    );

    const editorThemeChange$ = actions$.filter(
        R.propEq('type', EDITOR_THEME_CHANGE)
    );

    const user$ = merge([
        editorWidthChange$,
        editorInvisiblesToggle$,
        editorTabsToggle$,
        editorThemeChange$
    ]);

    return state$.sampledBy(user$).debounce(2500)
        .flatMapLatest((state: EditorPageState): Observable<ObsResponse> => ajax$(state.globals.root + 'me', selectUserAjaxOpts(state)))
        .flatMap((response: ObsResponse) => response.json())
        .flatten((response: UserApiResponse): Array<Action> => [ajaxFinishedAction(response), userSaveSucceededAction(response)])
        .mapErrors(ajaxFailedAction);
}
