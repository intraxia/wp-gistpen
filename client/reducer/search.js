// @flow
import type { SearchInputAction, SearchState } from '../type';
import { combineActionReducers } from 'brookjs';
import { SEARCH_INPUT } from '../action';

export default combineActionReducers([
    [SEARCH_INPUT, (state : SearchState, action : SearchInputAction) => ({
        ...state,
        term: action.payload.value
    })]
], { term: '' });
