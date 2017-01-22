// @flow
import type { Action, EditorPageState, UserApiResponse } from '../type';
import type { Observable } from 'kefir';
import R from 'ramda';
import { merge } from 'kefir';
import ajax$ from '../ajax';
import { ajaxFinishedAction, ajaxFailedAction, userSaveSucceededAction, EDITOR_WIDTH_CHANGE,
    EDITOR_INVISIBLES_TOGGLE, EDITOR_TABS_TOGGLE, EDITOR_THEME_CHANGE } from '../action';
import { selectUserAjaxOpts } from '../selector';

/**
 * User endpoint delta.
 *
 * @param {Observable<Action>} actions$ - Stream of store actions.
 * @param {Observable<EditorPageState>} state$ - Stream of store states.
 * @returns {Observable<Action>} Stream of actions.
 */
export default function userDelta(actions$ : Observable<Action>, state$ : Observable<EditorPageState>) : Observable<Action> {
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
        .flatMapLatest((state : EditorPageState) : Observable<string> => ajax$(state.api.root + 'me', selectUserAjaxOpts(state)))
        .map(JSON.parse)
        .flatten((response : UserApiResponse) : Array<Action> => [ajaxFinishedAction(response), userSaveSucceededAction(response)])
        .mapErrors(ajaxFailedAction);
}
