// @flow
import type { Action, AjaxFunction, HasApiConfig, HasRepo } from '../type';
import R from 'ramda';
import Kefir from 'kefir';
import { EDITOR_REVISIONS_CLICK } from '../action';

type RevisionsProps = HasRepo & HasApiConfig;
type RevisionsServices = {
    ajax$ : AjaxFunction;
};
type Commit = {};
type GetCommitsResponse = Array<Commit>;

const ofRevisionsClickType = R.pipe(R.prop('type'), R.equals(EDITOR_REVISIONS_CLICK));

export default R.curry((
    { ajax$ } : RevisionsServices,
    actions$ : Kefir.Observable<Action>,
    state$ : Kefir.Observable<RevisionsProps>
) => state$.sampledBy(actions$.filter(ofRevisionsClickType))
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
