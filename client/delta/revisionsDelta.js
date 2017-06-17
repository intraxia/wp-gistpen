// @flow
import type { Action, AjaxFunction, HasApiConfig, HasRepo, RouteChangeAction } from '../type';
import R from 'ramda';
import Kefir from 'kefir';
import { ROUTE_CHANGE } from '../action';

type RevisionsProps = HasRepo & HasApiConfig;
type RevisionsServices = {
    ajax$ : AjaxFunction;
};
type Commit = {};
type GetCommitsResponse = Array<Commit>;

export default R.curry((
    { ajax$ } : RevisionsServices,
    actions$ : ActionObservable<Action>,
    state$ : Kefir.Observable<RevisionsProps>
) => state$.sampledBy(actions$.ofType(ROUTE_CHANGE).filter((action : RouteChangeAction) => action.payload.route === 'revisions'))
    .filter((state : RevisionsProps) => state.repo.ID)
    .flatMapFirst((state : RevisionsProps) =>
        Kefir.concat([
            Kefir.constant({
                type: 'COMMITS_FETCH_STARTED'
            }),
            ajax$(state.repo.commits_url, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'X-WP-Nonce': state.api.nonce,
                    'Content-Type': 'application/json'
                }
            })
                .map(JSON.parse)
                .map((response : GetCommitsResponse) => ({
                    type: 'COMMITS_FETCH_SUCCEEDED',
                    payload: { response }
                }))
                .flatMapErrors((err : TypeError) => Kefir.constant({
                    type: 'COMMITS_FETCH_FAILED',
                    payload: err,
                    error: true
                })),
        ])
    ));
