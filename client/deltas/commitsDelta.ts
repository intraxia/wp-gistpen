import Kefir, { Stream, Property, Observable } from 'kefir';
import { ofType } from 'brookjs-flow';
import * as t from 'io-ts';
import { AjaxService } from '../ajax';
import {
  routeChange,
  commitsFetchStarted,
  commitsFetchSucceeded,
  commitsFetchFailed
} from '../actions';
import { RootAction } from '../util';
import { GlobalsState, RepoState } from '../reducers';
import { Nullable } from 'typescript-nullable';

type CommitsServices = {
  ajax$: AjaxService;
};

type CommitsDeltaState = {
  globals: GlobalsState;
  repo: RepoState;
};

const apiCommits = t.array(
  t.type({
    ID: t.number,
    description: t.string,
    committed_at: t.string,
    author: t.string,
    states: t.array(
      t.type({
        ID: t.number,
        code: t.string,
        filename: t.string,
        language: t.type({
          slug: t.string
        })
      })
    )
  })
);

export interface ApiCommits extends t.TypeOf<typeof apiCommits> {}

export const commitsDelta = ({ ajax$ }: CommitsServices) => (
  actions$: Stream<RootAction, never>,
  state$: Property<CommitsDeltaState, never>
): Observable<RootAction, never> =>
  state$
    .sampledBy(
      // sample when route changes to `commits`
      actions$
        .thru(ofType(routeChange))
        .filter(action => action.payload.name === 'commits')
    )
    .filter(state => state.repo != null && state.repo.ID != null)
    .flatMapFirst(state =>
      Kefir.concat<RootAction, never>([
        Kefir.constant(commitsFetchStarted()),
        ajax$(Nullable.maybe('', repo => repo.commits_url, state.repo), {
          method: 'GET',
          credentials: 'include',
          headers: {
            'X-WP-Nonce': state.globals.nonce,
            'Content-Type': 'application/json'
          }
        })
          .flatMap(response => response.json())
          .flatMap(response =>
            apiCommits.is(response)
              ? Kefir.constant(commitsFetchSucceeded(response))
              : Kefir.constantError(new TypeError('Response did not match'))
          )
          .flatMapErrors(err => Kefir.constant(commitsFetchFailed(err)))
      ])
    );
