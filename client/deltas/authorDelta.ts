import Kefir, { Stream, Property, Observable } from 'kefir';
import { ofType } from 'brookjs-flow';
import * as t from 'io-ts';
import {
  fetchAuthorSucceeded,
  fetchAuthorFailed,
  commitsFetchSucceeded
} from '../actions';
import { CommitsState, GlobalsState } from '../reducers';
import { AjaxService } from '../ajax';
import { RootAction } from '../util';

type AuthorServices = {
  ajax$: AjaxService;
};

type AuthorDeltaState = {
  commits: CommitsState;
  globals: GlobalsState;
};

const apiAuthor = t.type({
  id: t.number,
  name: t.string,
  url: t.string,
  description: t.string,
  link: t.string,
  slug: t.string,
  avatar_urls: t.dictionary(t.string, t.string)
});

export interface ApiAuthor extends t.TypeOf<typeof apiAuthor> {}

export const authorDelta = ({ ajax$ }: AuthorServices) => (
  actions$: Stream<RootAction, never>,
  state$: Property<AuthorDeltaState, never>
): Observable<RootAction, never> =>
  state$
    .sampledBy(actions$.thru(ofType(commitsFetchSucceeded)))
    .flatMapLatest(state =>
      Kefir.merge(
        state.commits.instances.map(instance =>
          ajax$(`/wp-json/wp/v2/users/${instance.author}`, {
            method: 'GET',
            credentials: 'include',
            headers: {
              'X-WP-Nonce': state.globals.nonce,
              'Content-Type': 'application/json'
            }
          })
        )
      )
        .flatMap(response => response.json())
        .flatMap(response =>
          apiAuthor.is(response)
            ? Kefir.constant(fetchAuthorSucceeded(response))
            : Kefir.constantError(
                new TypeError('Author response was not the expected shape')
              )
        )
        .flatMapErrors(err => Kefir.constant(fetchAuthorFailed(err)))
    );
