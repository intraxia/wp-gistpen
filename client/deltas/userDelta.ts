import Kefir, { Observable } from 'kefir';
import { ofType } from 'brookjs';
import * as t from 'io-ts';
import { ajax$ } from 'kefir-ajax';
import {
  ajaxFinished,
  ajaxFailed,
  userSaveSucceeded,
  editorInvisiblesToggle,
  editorTabsToggle,
  editorThemeChange,
} from '../actions';
import { selectUserAjaxOpts } from '../selectors';
import { RootAction } from '../RootAction';
import { EditorState } from '../reducers';
import { GlobalsState } from '../globals';
import { ValidationError, JsonError } from '../api';
import { editorWidthChange } from '../editor/actions';

export type UserDeltaState = {
  globals: GlobalsState;
  editor: EditorState;
};

type UserDeltaServices = {
  ajax$: typeof ajax$;
};

const userResponse = t.type({});

export type UserApiResponse = t.TypeOf<typeof userResponse>;

export const userDelta = ({ ajax$ }: UserDeltaServices) => (
  actions$: Observable<RootAction, never>,
  state$: Observable<UserDeltaState, never>,
): Observable<RootAction, never> =>
  state$
    .sampledBy(
      actions$.thru(
        ofType(
          editorWidthChange,
          editorInvisiblesToggle,
          editorTabsToggle,
          editorThemeChange,
        ),
      ),
    )
    .debounce(2500)
    .flatMapLatest(state =>
      ajax$(state.globals.root + 'me', selectUserAjaxOpts(state)),
    )
    .flatMap(response => response.json().mapErrors(err => new JsonError(err)))
    .flatMap(response =>
      userResponse
        .validate(response, [])
        .fold<Observable<t.TypeOf<typeof userResponse>, ValidationError>>(
          errs => Kefir.constantError(new ValidationError(errs)),
          response => Kefir.constant(response),
        ),
    )
    .flatten(response => [ajaxFinished(), userSaveSucceeded(response)])
    .flatMapErrors(err => Kefir.constant(ajaxFailed(err)));
