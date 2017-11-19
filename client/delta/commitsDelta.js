// @flow
import type { Action, HasGlobalsState, HasRepo, RouteChangeAction } from '../type';
import type { AjaxService, ObsResponse } from '../service';
import R from 'ramda';
import Kefir from 'kefir';
import { ofType } from 'brookjs';
import { ROUTE_CHANGE, COMMITS_FETCH_SUCCEEDED, COMMITS_FETCH_STARTED,
    COMMITS_FETCH_FAILED } from '../action';

type CommitsProps = HasRepo & HasGlobalsState;
type CommitsServices = {
    ajax$ : AjaxService;
};
type Commit = {};
type GetCommitsResponse = Array<Commit>;

export default R.curry((
    { ajax$ } : CommitsServices,
    actions$ : ActionObservable<Action>,
    state$ : Kefir.Observable<CommitsProps>
) : Kefir.Observable<Action> => {
    const fetchCommits$ = state$.sampledBy(
        // sample when route changes to `commits`
        actions$.thru(ofType(ROUTE_CHANGE)).filter((action : RouteChangeAction) => action.payload.name === 'commits')
    )
        .filter((state : CommitsProps) => state.repo.ID)
        .flatMapFirst((state : CommitsProps) =>
            Kefir.concat([
                Kefir.constant({
                    type: COMMITS_FETCH_STARTED
                }),
                ajax$(state.repo.commits_url, {
                    method: 'GET',
                    credentials: 'include',
                    headers: {
                        'X-WP-Nonce': state.globals.nonce,
                        'Content-Type': 'application/json'
                    }
                })
                    .flatMap((response : ObsResponse) => response.json())
                    .map((response : GetCommitsResponse) => ({
                        type: COMMITS_FETCH_SUCCEEDED,
                        payload: { response }
                    }))
                    .flatMapErrors((err : TypeError) => Kefir.constant({
                        type: COMMITS_FETCH_FAILED,
                        payload: err,
                        error: true
                    })),
            ])
        );

    return Kefir.merge([
        fetchCommits$
    ]);
});
