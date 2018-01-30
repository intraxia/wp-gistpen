// @flow
import type { Observable } from 'kefir';
import type { TinyMCEState } from '../reducers';
import type { Action } from '../types';
import type { ObsResponse } from '../services';
import R from 'ramda';
import { ajax$ } from '../services';
import { SEARCH_INPUT, searchResultsSucceededAction } from '../actions';

function getSearchUrl(state: TinyMCEState): string {
    return `${state.globals.root}search?s=${state.search.term}`;
}
export default function searchDelta(actions$: Observable<Action>, state$: Observable<TinyMCEState>): Observable<Action> {
    return state$.sampledBy(actions$.filter(R.pipe(R.prop('type'), R.equals(SEARCH_INPUT))))
        .flatMapLatest((state: TinyMCEState): Observable<ObsResponse, TypeError> => ajax$(getSearchUrl(state), {
            method: 'GET',
            headers: {
                'X-WP-Nonce': state.globals.nonce
            }
        }))
        .flatMap((response: ObsResponse) => response.json())
        .map(searchResultsSucceededAction);
}
