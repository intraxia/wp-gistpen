// @flow
import type { SearchInputAction } from '../type';

export const SEARCH_INPUT = 'SEARCH_INPUT';

export function searchInputAction(value : string) : SearchInputAction {
    return {
        type: SEARCH_INPUT,
        payload: { value }
    };
}
