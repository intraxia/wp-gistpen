import { Delta, sampleByAction, ofType } from 'brookjs';
import Kefir, { Observable } from 'kefir';
import * as t from 'io-ts';
import { RootAction } from '../util';
import { AjaxError, ajax$ } from '../ajax';
import { validationErrorsToString } from '../api';
import { search } from './actions';

type SearchDeltaState = {
  root: string;
  nonce: string;
  term: string;
};

export const SearchBlob = t.type({
  ID: t.number,
  filename: t.string,
  code: t.string,
  repo_id: t.number,
  language: t.type({
    ID: t.number,
    display_name: t.string,
    slug: t.string,
  }),
  rest_url: t.string,
  repo_rest_url: t.string,
});

export type SearchBlob = t.TypeOf<typeof SearchBlob>;

export const SearchApiResponse = t.array(SearchBlob);

export type SearchApiResponse = t.TypeOf<typeof SearchApiResponse>;

const getSearchUrl = (state: SearchDeltaState) =>
  `${state.root}search/blobs?s=${state.term}`;

export const searchDelta: Delta<RootAction, SearchDeltaState> = (
  actions$,
  state$,
) =>
  state$
    .thru(sampleByAction(actions$, search.request))
    .filter(state => state.term !== '')
    .debounce(300)
    .flatMapLatest(state =>
      ajax$(getSearchUrl(state), {
        method: 'GET',
        headers: {
          'X-WP-Nonce': state.nonce,
        },
      }).takeUntilBy(actions$.thru(ofType(search.cancel))),
    )
    .flatMap(response => response.json())
    .flatMap(response =>
      SearchApiResponse.validate(response, []).fold<
        Observable<RootAction, AjaxError>
      >(
        errs =>
          Kefir.constantError(new AjaxError(validationErrorsToString(errs))),
        res => Kefir.constant(search.success(res)),
      ),
    )
    .flatMapErrors(err => Kefir.constant(search.failure(err)));
