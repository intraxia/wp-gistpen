// @flow
import type { Observable } from 'kefir';
import type { Action, TinyMCEState } from '../type';
import type { ObsResponse } from '../service';
import R from 'ramda';
import { ajax$ } from '../service';
import { SEARCH_INPUT, searchResultsSucceededAction } from '../action';

function getSearchUrl(state : TinyMCEState) : string {
    return `${state.globals.root}search?s=${state.search.term}`;
}
export default function searchDelta(actions$ : Observable<Action>, state$ : Observable<TinyMCEState>) : Observable<Action> {
    return state$.sampledBy(actions$.filter(R.pipe(R.prop('type'), R.equals(SEARCH_INPUT))))
        .flatMapLatest((state : TinyMCEState) : Observable<ObsResponse, TypeError> => ajax$(getSearchUrl(state), {
            method: 'GET',
            headers: {
                'X-WP-Nonce': state.globals.nonce
            }
        }))
        .flatMap((response : ObsResponse) => response.json())
        .map(searchResultsSucceededAction);
}
