// @flow
import type { SearchInputAction, SearchResultSelectionChangeAction } from '../types';

export const SEARCH_INPUT = 'SEARCH_INPUT';

export function searchInputAction(value : string) : SearchInputAction {
    return {
        type: SEARCH_INPUT,
        payload: { value }
    };
}

export const SEARCH_RESULT_SELECTION_CHANGE = 'SEARCH_RESULT_SELECTION_CHANGE';

export function searchResultSelectionChangeAction(selection : string) : SearchResultSelectionChangeAction {
    return {
        type: SEARCH_RESULT_SELECTION_CHANGE,
        payload: { selection }
    };
}
