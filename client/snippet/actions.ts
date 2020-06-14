import { createAsyncAction } from 'typesafe-actions';
import { AjaxError } from '../api';
import { ApiBlob } from './types';

export const fetchBlob = createAsyncAction(
  'FETCH_BLOB_REQUESTED',
  'FETCH_BLOB_SUCCEEDED',
  'FETCH_BLOB_FAILED',
)<
  {
    repoId: number;
    blobId: number;
  },
  ApiBlob,
  AjaxError
>();
