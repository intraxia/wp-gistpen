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

const searchResponse = t.array(
  t.type({
    ID: t.number,
    filename: t.string,
    code: t.string,
    language: t.type({
      ID: t.number,
      display_name: t.string,
      slug: t.string
    })
  })
);

export type SearchApiResponse = t.TypeOf<typeof searchResponse>;

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
      searchResponse.validate(response, []).fold<Observable<RootAction, Error>>(
        errs =>
          Kefir.constantError(
            new Error(`Search API response validation failed:

${errs
  .map(
    err =>
      `* Invalid value ${JSON.stringify(
        err.value
      )} supplied to ${err.context.map(x => x.key).join('/')}`
  )
  .join('\n')}`)
          ),
        res => Kefir.constant(searchResultsSucceeded(res))
      )
    )
    .flatMapErrors(err => Kefir.constant(searchsResultsFailed(err)));
