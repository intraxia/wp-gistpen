import {
  searchInput,
  searchResultsSucceeded,
  searchResultSelectionChange
} from '../actions';
import { RootAction } from '../util';
import { getType } from 'typesafe-actions';

export type SearchState = {
  term: string;
  selection: number | null;
  results: Array<Blob>;
};

const defaultState: SearchState = { term: '', results: [], selection: null };

export const searchReducer = (
  state: SearchState = defaultState,
  action: RootAction
) => {
  switch (action.type) {
    case getType(searchInput):
      return {
        ...state,
        term: action.payload.value,
        results: action.payload.value ? state.results : []
      };
    case getType(searchResultsSucceeded):
      return {
        ...state,
        results: action.payload.response,
        selection: null
      };
    case getType(searchResultSelectionChange):
      return {
        ...state,
        selection: parseInt(action.payload.selection, 10)
      };
    default:
      return state;
  }
};
