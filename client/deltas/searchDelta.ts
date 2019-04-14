// @flow
import Kefir, { Observable } from 'kefir';
import { RootAction } from '../util';
import { ofType } from 'brookjs';
import * as t from 'io-ts';
import { AjaxService } from '../ajax';
import {
  searchInput,
  searchResultsSucceeded,
  searchsResultsFailed
} from '../actions';
import { GlobalsState, SearchState } from '../reducers';

type SearchDeltaState = {
  globals: GlobalsState;
  search: SearchState;
};

type SearchDeltaServices = {
  ajax$: AjaxService;
};

const searchResponse = t.type({});

const getSearchUrl = (state: SearchDeltaState) =>
  `${state.globals.root}search?s=${state.search.term}`;

export const searchDelta = ({ ajax$ }: SearchDeltaServices) => (
  actions$: Observable<RootAction, never>,
  state$: Observable<SearchDeltaState, never>
): Observable<RootAction, never> =>
  state$
    .sampledBy(actions$.thru(ofType(searchInput)))
    .filter(state => state.search.term !== '')
    .flatMapLatest(state =>
      ajax$(getSearchUrl(state), {
        method: 'GET',
        headers: {
          'X-WP-Nonce': state.globals.nonce
        }
      })
    )
    .flatMap(response => response.json())
    .flatMap(response =>
      searchResponse
        .validate(response, [])
        .fold<Observable<RootAction, Error>>(
          res => Kefir.constant(searchResultsSucceeded(res)),
          () => Kefir.constantError(new Error('API response validation failed'))
        )
    )
    .flatMapErrors(err => Kefir.constant(searchsResultsFailed(err)));
