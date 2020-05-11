import { createAction, createAsyncAction } from 'typesafe-actions';
import { NetworkError } from 'kefir-ajax';
import { ValidationError } from '../api';
import { SearchApiResponse, SearchBlob, SearchRepo } from './delta';

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
)<void, SearchApiResponse, NetworkError | TypeError | ValidationError, void>();

export const searchBlobSelected = createAction(
  'SEARCH_BLOB_SELECTED',
  resolve => (blob: SearchBlob) => resolve({ blob }),
);

export const searchRepoSelected = createAction(
  'SEARCH_REPO_SELECTED',
  resolve => (repo: SearchRepo) => resolve({ repo }),
);
