// @flow
import type { Observable } from 'kefir';
import type { TinyMCEState } from '../reducers';
import type { Action } from '../types';
import type { ObsResponse } from '../services';
import { ofType } from 'brookjs';
import { ajax$ } from '../ajax';
import { searchInput, searchResultsSucceeded } from '../actions';

function getSearchUrl(state: TinyMCEState): string {
    return `${state.globals.root}search?s=${state.search.term}`;
}
export default function searchDelta(actions$: Observable<Action>, state$: Observable<TinyMCEState>): Observable<Action> {
    return state$.sampledBy(actions$.thru(ofType(searchInput)))
        .filter((state: TinyMCEState) => state.search.term)
        .flatMapLatest((state: TinyMCEState): Observable<ObsResponse, TypeError> => ajax$(getSearchUrl(state), {
            method: 'GET',
            headers: {
                'X-WP-Nonce': state.globals.nonce
            }
        }))
        .flatMap((response: ObsResponse) => response.json())
        .map(searchResultsSucceeded);
}
