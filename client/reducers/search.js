// @flow
import type { SearchInputAction, SearchResultsSucceededAction, SearchResultSelectionChangeAction, SearchState } from '../types';
import { combineActionReducers } from 'brookjs';
import { SEARCH_INPUT, SEARCH_RESULTS_SUCCEEDED, SEARCH_RESULT_SELECTION_CHANGE } from '../actions';

export default combineActionReducers([
    [SEARCH_INPUT, (state : SearchState, action : SearchInputAction) : SearchState => ({
        ...state,
        term: action.payload.value
    })],
    [SEARCH_RESULTS_SUCCEEDED, (state : SearchState, action : SearchResultsSucceededAction) : SearchState  => ({
        ...state,
        results: action.payload.response,
        selection: undefined
    })],
    [SEARCH_RESULT_SELECTION_CHANGE, (state : SearchState, action : SearchResultSelectionChangeAction) : SearchState  => ({
        ...state,
        selection: parseInt(action.payload.selection, 10)
    })]
], { term: '', results: false });
