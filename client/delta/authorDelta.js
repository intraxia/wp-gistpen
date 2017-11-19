// @flow
import type { Action, CommitsState, GlobalsState, CommitState } from '../type';
import type { AjaxService, ObsResponse } from '../service';
import type { Author } from '../type';
import Kefir from 'kefir';
import { ofType } from 'brookjs';
import { fetchAuthorSucceeded, fetchAuthorFailed, COMMITS_FETCH_SUCCEEDED } from '../action';

type AuthorServices = {
    ajax$ : AjaxService;
};

type AuthorApiResponse = Author;

type AuthorDeltaState = {
    commits : CommitsState;
    globals : GlobalsState;
};

export default ({ ajax$ } : AuthorServices) =>
    (actions$ : Kefir.Observable<Action>, state$ : Kefir.Observable<AuthorDeltaState>) =>
        state$.sampledBy(
            actions$.thru(ofType(COMMITS_FETCH_SUCCEEDED))
        )
            .flatMapLatest((state : AuthorDeltaState) => Kefir.merge(
                state.commits.instances.map((instance : CommitState) =>
                    ajax$(`/wp-json/wp/v2/users/${instance.author}`, {
                        method: 'GET',
                        credentials: 'include',
                        headers: {
                            'X-WP-Nonce': state.globals.nonce,
                            'Content-Type': 'application/json'
                        }
                    })
                        .flatMap((response : ObsResponse) => response.json())
                        .map((response : AuthorApiResponse) => fetchAuthorSucceeded(response))
                        .flatMapErrors((err : TypeError) => Kefir.constant(fetchAuthorFailed(err))))
            ));
