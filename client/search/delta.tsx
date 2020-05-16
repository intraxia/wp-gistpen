import { Delta, sampleByAction, ofType } from 'brookjs';
import Kefir, { Observable } from 'kefir';
import * as t from 'io-ts';
import { ajax$ } from 'kefir-ajax';
import { RootAction, toggle } from '../util';
import { ValidationError, JsonError } from '../api';
import { search } from './actions';
import { Collection, RepoCollection, BlobCollection } from './state';

type SearchDeltaState = {
  root: string;
  nonce: string;
  term: string;
  collection: Collection;
};

export const SearchRepo = t.type({
  ID: t.number,
  description: t.string,
  slug: t.string,
  status: t.string,
  password: t.string,
  gist_id: t.union([t.string, t.null]),
  gist_url: t.string,
  sync: toggle,
  blobs: t.array(
    t.type({
      ID: t.number,
      filename: t.string,
      rest_url: t.string,
    }),
  ),
  rest_url: t.string,
  commits_url: t.string,
  html_url: t.string,
  created_at: t.string,
  updated_at: t.string,
});

export type SearchRepo = t.TypeOf<typeof SearchRepo>;

export const SearchReposApiResponse = t.array(SearchRepo);

export type SearchReposApiResponse = t.TypeOf<typeof SearchReposApiResponse>;

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

export const SearchBlobsApiResponse = t.array(SearchBlob);

export type SearchBlobsApiResponse = t.TypeOf<typeof SearchBlobsApiResponse>;

export const SearchApiResponse = t.union([
  t.type({
    collection: RepoCollection,
    response: SearchReposApiResponse,
  }),
  t.type({
    collection: BlobCollection,
    response: SearchBlobsApiResponse,
  }),
]);

export type SearchApiResponse = t.TypeOf<typeof SearchApiResponse>;

const getSearchUrl = (state: SearchDeltaState) =>
  `${state.root}search/${state.collection}?s=${encodeURIComponent(state.term)}`;

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
      })
        .takeUntilBy(actions$.thru(ofType(search.cancel)))
        .flatMap(response =>
          response.json().mapErrors(error => new JsonError(error)),
        )
        .flatMap(response =>
          SearchApiResponse.validate(
            { collection: state.collection, response },
            [],
          ).fold<Observable<RootAction, ValidationError>>(
            errs => Kefir.constantError(new ValidationError(errs)),
            res => Kefir.constant(search.success(res)),
          ),
        ),
    )
    .flatMapErrors(err => Kefir.constant(search.failure(err)));
