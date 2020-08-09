import Kefir, { Observable } from 'kefir';
import { ofType } from 'brookjs';
import { ajax$ } from 'kefir-ajax';
import { RootAction } from '../RootAction';
import { SearchState } from '../reducers';
import { ValidationError, JsonError } from '../api';
import { actions as searchActions, SearchApiResponse } from '../search';
import { GlobalsState } from '../globals';

type SearchDeltaState = {
  globals: GlobalsState;
  search: SearchState;
};

type SearchDeltaServices = {
  ajax$: typeof ajax$;
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
    .flatMap(response => response.json().mapErrors(err => new JsonError(err)))
    .flatMap(response =>
      SearchApiResponse.validate(response, []).fold<
        Observable<RootAction, ValidationError>
      >(
        errs => Kefir.constantError(new ValidationError(errs)),
        res => Kefir.constant(searchActions.search.success(res)),
      ),
    )
    .flatMapErrors(err => Kefir.constant(searchActions.search.failure(err)));
