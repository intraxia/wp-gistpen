import { getType } from 'typesafe-actions';
import { EddyReducer } from 'brookjs';
import {
  searchInput,
  searchResultsSucceeded,
  searchResultSelectionChange
} from '../actions';
import { RootAction } from '../util';

type Language = {
  ID: number;
  display_name: string;
  slug: string;
};

type Blob = {
  filename: string;
  code: string;
  language: Language;
  ID: number;
  size?: number;
  raw_url?: string;
  edit_url?: string;
};

export type SearchState = {
  term: string;
  selection: number | null;
  results: Array<Blob>;
};

const defaultState: SearchState = { term: '', results: [], selection: null };

export const searchReducer: EddyReducer<SearchState, RootAction> = (
  state = defaultState,
  action
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
