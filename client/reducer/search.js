// @flow
import type { SearchInputAction, SearchResultsSucceededAction, SearchState } from '../type';
import { combineActionReducers } from 'brookjs';
import { SEARCH_INPUT, SEARCH_RESULTS_SUCCEEDED } from '../action';

export default combineActionReducers([
    [SEARCH_INPUT, (state : SearchState, action : SearchInputAction) => ({
        ...state,
        term: action.payload.value
    })],
    [SEARCH_RESULTS_SUCCEEDED, (state : SearchState, action : SearchResultsSucceededAction) => ({
        ...state,
        results: action.payload.response
    })]
], { term: '', results: false });
