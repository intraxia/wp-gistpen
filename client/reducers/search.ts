import { getType } from 'typesafe-actions';
import { EddyReducer } from 'brookjs';
import { RootAction } from '../util';
import { actions as searchActions } from '../search';

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
  action,
) => {
  switch (action.type) {
    case getType(searchActions.searchInput):
      return {
        ...state,
        term: action.payload.value,
        results: action.payload.value ? state.results : [],
      };
    case getType(searchActions.search.success):
      return {
        ...state,
        results: action.payload,
        selection: null,
      };
    case getType(searchActions.searchResultSelectionChange):
      return {
        ...state,
        selection: action.payload.ID,
      };
    default:
      return state;
  }
};
