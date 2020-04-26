import { createAction, createAsyncAction } from 'typesafe-actions';
import { AjaxError } from '../ajax';
import { SearchApiResponse, SearchBlob } from './delta';

export const searchInput = createAction(
  'SEARCH_INPUT',
  resolve => (value: string) => resolve({ value }),
);

export const searchResultSelectionChange = createAction(
  'SEARCH_RESULT_SELECTION_CHANGE',
  resolve => (ID: number) => resolve({ ID }),
);

export const searchResultSelectClick = createAction(
  'SEARCH_RESULT_SELECT_CLICK',
);

export const search = createAsyncAction(
  'SEARCH_REQUESTED',
  'SEARCH_SUCCESS',
  'SEARCH_FAILED',
  'SEARCH_CANCELED',
)<void, SearchApiResponse, TypeError | AjaxError, void>();

export const snippetSelected = createAction(
  'SNIPPET_SELECTED',
  resolve => (blob: SearchBlob) => resolve({ blob }),
);
