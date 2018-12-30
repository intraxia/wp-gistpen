// @flow
import type { Action, HasGlobalsState, HasRepo, RouteChangeAction } from '../types';
import type { AjaxService, ObsResponse } from '../services';
import R from 'ramda';
import Kefir from 'kefir';
import { ofType } from 'brookjs';
import { routeChange, commitsFetchStarted, commitsFetchSucceeded,
    commitsFetchFailed } from '../actions';

type CommitsProps = HasRepo & HasGlobalsState;
type CommitsServices = {
    ajax$: AjaxService
};
type Commit = {};
type GetCommitsResponse = Array<Commit>;

export default R.curry((
    { ajax$ }: CommitsServices,
    actions$: Kefir.Observable<Action>,
    state$: Kefir.Observable<CommitsProps>
): Kefir.Observable<Action> => {
    const fetchCommits$ = state$.sampledBy(
        // sample when route changes to `commits`
        actions$.thru(ofType(routeChange)).filter((action: RouteChangeAction) => action.payload.name === 'commits')
    )
        .filter((state: CommitsProps) => state.repo.ID)
        .flatMapFirst((state: CommitsProps) =>
            Kefir.concat([
                Kefir.constant(commitsFetchStarted()),
                ajax$(state.repo.commits_url, {
                    method: 'GET',
                    credentials: 'include',
                    headers: {
                        'X-WP-Nonce': state.globals.nonce,
                        'Content-Type': 'application/json'
                    }
                })
                    .flatMap((response: ObsResponse) => response.json())
                    .map((response: GetCommitsResponse) => commitsFetchSucceeded(response))
                    .flatMapErrors((err: TypeError) => commitsFetchFailed(err)),
            ])
        );

    return Kefir.merge([
        fetchCommits$
    ]);
});
