// @flow
import type { Action, Commit, CommitsState, GlobalsState } from '../types';
import type { AjaxService, ObsResponse } from '../ajax';
import type { Author } from '../types';
import Kefir from 'kefir';
import { ofType } from 'brookjs';
import { fetchAuthorSucceeded, fetchAuthorFailed, commitsFetchSucceeded } from '../actions';

type AuthorServices = {
    ajax$: AjaxService
};

type AuthorApiResponse = Author;

type AuthorDeltaState = {
    commits: CommitsState,
    globals: GlobalsState
};

export default ({ ajax$ }: AuthorServices) =>
    (actions$: Kefir.Observable<Action>, state$: Kefir.Observable<AuthorDeltaState>) =>
        state$.sampledBy(
            actions$.thru(ofType(commitsFetchSucceeded))
        )
            .flatMapLatest((state: AuthorDeltaState) => Kefir.merge(
                state.commits.instances.map((instance: Commit) =>
                    ajax$(`/wp-json/wp/v2/users/${instance.author}`, {
                        method: 'GET',
                        credentials: 'include',
                        headers: {
                            'X-WP-Nonce': state.globals.nonce,
                            'Content-Type': 'application/json'
                        }
                    })
                        .flatMap((response: ObsResponse) => response.json())
                        .map((response: AuthorApiResponse) => fetchAuthorSucceeded(response))
                        .flatMapErrors((err: TypeError) => Kefir.constant(fetchAuthorFailed(err))))
            ));
