import Kefir, { Observable } from 'kefir';
import { ofType } from 'brookjs';
import { RootAction } from '../util';
import { AjaxService, AjaxError } from '../ajax';
import { SearchState } from '../reducers';
import { validationErrorsToString } from '../api';
import { actions as searchActions, SearchApiResponse } from '../search';
import { GlobalsState } from '../globals';

type SearchDeltaState = {
  globals: GlobalsState;
  search: SearchState;
};

type SearchDeltaServices = {
  ajax$: AjaxService;
};

const getSearchUrl = (state: SearchDeltaState) =>
  `${state.globals.root}search?s=${state.search.term}`;

export const searchDelta = ({ ajax$ }: SearchDeltaServices) => (
  actions$: Observable<RootAction, never>,
  state$: Observable<SearchDeltaState, never>,
): Observable<RootAction, never> =>
  state$
    .sampledBy(actions$.thru(ofType(searchActions.searchInput)))
    .filter(state => state.search.term !== '')
    .flatMapLatest(state =>
      ajax$(getSearchUrl(state), {
        method: 'GET',
        headers: {
          'X-WP-Nonce': state.globals.nonce,
        },
      }),
    )
    .flatMap(response => response.json())
    .flatMap(response =>
      SearchApiResponse.validate(response, []).fold<
        Observable<RootAction, AjaxError>
      >(
        errs =>
          Kefir.constantError(new AjaxError(validationErrorsToString(errs))),
        res => Kefir.constant(searchActions.search.success(res)),
      ),
    )
    .flatMapErrors(err => Kefir.constant(searchActions.search.failure(err)));
