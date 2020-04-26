import { loop, EddyReducer } from 'brookjs';
import { getType } from 'typesafe-actions';
import { RootAction } from '../util';
import { GlobalsState, defaultGlobals } from '../reducers';
import {
  searchResultSelectionChange,
  searchInput,
  search,
  snippetSelected,
} from './actions';
import { SearchApiResponse } from './delta';

export type Initial = {
  status: 'initial';
  term: string;
  globals: GlobalsState;
};

export type Searching = {
  status: 'searching';
  term: string;
  globals: GlobalsState;
};

export type Found = {
  status: 'found';
  snippets: SearchApiResponse;
  term: string;
  globals: GlobalsState;
};

export type Error = {
  status: 'error';
  error: string;
  term: string;
  globals: GlobalsState;
};

export type Researching = {
  status: 'researching';
  snippets: SearchApiResponse;
  term: string;
  globals: GlobalsState;
};

export type Reerror = {
  status: 'reerror';
  error: string;
  snippets: SearchApiResponse;
  term: string;
  globals: GlobalsState;
};

export type State = Initial | Searching | Found | Error | Researching | Reerror;

export type HasError = Error | Reerror;

export type HasSnippet = Found | Researching | Reerror;

export const initialState: State = {
  status: 'initial',
  term: '',
  globals: defaultGlobals,
};

export const reducer: EddyReducer<State, RootAction> = (
  state: State = initialState,
  action: RootAction,
) => {
  switch (action.type) {
    case getType(searchInput):
      return loop(
        {
          ...state,
          term: action.payload.value,
        },
        // Clean up open requests if the user clears the input.
        action.payload.value === '' ? search.cancel() : search.request(),
      );
    case getType(searchResultSelectionChange):
      switch (state.status) {
        case 'found':
        case 'researching':
        case 'reerror':
          return loop(
            state,
            snippetSelected(
              state.snippets.find(snippet => snippet.ID === action.payload.ID)!,
            ),
          );
        default:
          return state;
      }
    case getType(search.request): {
      switch (state.status) {
        case 'initial':
        case 'error':
          return {
            term: state.term,
            status: 'searching',
            globals: state.globals,
          } as const;
        case 'found':
        case 'reerror':
          return {
            term: state.term,
            status: 'researching',
            snippets: state.snippets,
            globals: state.globals,
          } as const;
        default:
          return state;
      }
    }
    case getType(search.success):
      return {
        term: state.term,
        status: 'found',
        snippets: action.payload,
        globals: state.globals,
      } as const;
    case getType(search.failure):
      switch (state.status) {
        case 'searching':
          return {
            status: 'error',
            term: state.term,
            error: action.payload.message,
            globals: state.globals,
          } as const;
        case 'researching':
          return {
            status: 'reerror',
            term: state.term,
            snippets: state.snippets,
            error: action.payload.message,
            globals: state.globals,
          } as const;
        default:
          return state;
      }
    default:
      return state;
  }
};
