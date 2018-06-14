// @flow
import type { Reducer } from 'redux';
import type { SearchInputAction, SearchResultsSucceededAction, SearchResultSelectionChangeAction, Blob } from '../types';
import { combineActionReducers } from 'brookjs';
import { SEARCH_INPUT, SEARCH_RESULTS_SUCCEEDED, SEARCH_RESULT_SELECTION_CHANGE } from '../actions';

export type SearchState = {
    term: string,
    selection: number | null,
    results: Array<Blob>
};

export const searchReducer : Reducer<SearchState, *> = combineActionReducers([
    [SEARCH_INPUT, (state: SearchState, action: SearchInputAction): SearchState => ({
        ...state,
        term: action.payload.value,
        results: action.payload.value ? state.results : []
    })],
    [SEARCH_RESULTS_SUCCEEDED, (state: SearchState, action: SearchResultsSucceededAction): SearchState  => ({
        ...state,
        results: action.payload.response,
        selection: null
    })],
    [SEARCH_RESULT_SELECTION_CHANGE, (state: SearchState, action: SearchResultSelectionChangeAction): SearchState  => ({
        ...state,
        selection: parseInt(action.payload.selection, 10)
    })]
], { term: '', results: [], selection: null });
