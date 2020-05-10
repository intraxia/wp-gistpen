import { loop, EddyReducer } from 'brookjs';
import { getType } from 'typesafe-actions';
import * as t from 'io-ts';
import { RootAction } from '../util';
import { GlobalsState, defaultGlobals } from '../globals';
import {
  searchResultSelectionChange,
  searchInput,
  search,
  searchBlobSelected,
  searchRepoSelected,
} from './actions';
import { SearchApiResponse } from './delta';

export const BlobCollection = t.literal('blobs');

export const RepoCollection = t.literal('repos');

export const Collection = t.union([BlobCollection, RepoCollection]);

export type Collection = t.TypeOf<typeof Collection>;

export type Initial = {
  status: 'initial';
  term: string;
  collection: Collection;
  globals: GlobalsState;
};

export type Searching = {
  status: 'searching';
  term: string;
  collection: Collection;
  globals: GlobalsState;
};

export type Found = {
  status: 'found';
  results: SearchApiResponse;
  term: string;
  collection: Collection;
  globals: GlobalsState;
};

export type Error = {
  status: 'error';
  error: string;
  term: string;
  collection: Collection;
  globals: GlobalsState;
};

export type Researching = {
  status: 'researching';
  results: SearchApiResponse;
  term: string;
  collection: Collection;
  globals: GlobalsState;
};

export type Reerror = {
  status: 'reerror';
  error: string;
  results: SearchApiResponse;
  term: string;
  collection: Collection;
  globals: GlobalsState;
};

export type State = Initial | Searching | Found | Error | Researching | Reerror;

export type HasError = Error | Reerror;

export type HasSnippet = Found | Researching | Reerror;

export const initialState: State = {
  status: 'initial',
  collection: 'blobs',
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
            state.results.collection === 'blobs'
              ? searchBlobSelected(
                  state.results.response.find(
                    blob => blob.ID === action.payload.ID,
                  )!,
                )
              : searchRepoSelected(
                  state.results.response.find(
                    repo => repo.ID === action.payload.ID,
                  )!,
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
            collection: state.collection,
            term: state.term,
            status: 'searching',
            globals: state.globals,
          } as const;
        case 'found':
        case 'reerror':
          return {
            collection: state.collection,
            term: state.term,
            status: 'researching',
            results: state.results,
            globals: state.globals,
          } as const;
        default:
          return state;
      }
    }
    case getType(search.success):
      return {
        collection: state.collection,
        term: state.term,
        status: 'found',
        results: action.payload,
        globals: state.globals,
      } as const;
    case getType(search.failure):
      switch (state.status) {
        case 'searching':
          return {
            collection: state.collection,
            status: 'error',
            term: state.term,
            error: action.payload.message,
            globals: state.globals,
          } as const;
        case 'researching':
          return {
            collection: state.collection,
            status: 'reerror',
            term: state.term,
            results: state.results,
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
