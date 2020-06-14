import { createAction, createAsyncAction } from 'typesafe-actions';
import { AjaxError } from '../api';
import { ApiBlob, ApiRepo } from '../snippet';

export const createNewClick = createAction('CREATE_NEW_CLICK');

export const chooseExistingClick = createAction('CHOOSE_EXISTING_CLICK');

export const createRepoClick = createAction('CREATE_REPO_CLICK');

export const createDescriptionChange = createAction(
  'CREATE_DESCRIPTION_CHANGE',
  resolve => (value: string) => resolve({ value }),
);

export const createFilenameChange = createAction(
  'CREATE_FILENAME_CHANGE',
  resolve => (value: string) => resolve({ value }),
);

export const appendBlob = createAsyncAction(
  'APPEND_BLOB_REQUESTED',
  'APPEND_BLOB_SUCCEEDED',
  'APPEND_BLOB_FAILED',
)<number, { repoId: number; blob: ApiBlob }, AjaxError>();

export const newBlobAttached = createAction(
  'NEW_BLOB_ATTACHED',
  resolve => (blobId: number, repoId: number) => resolve({ blobId, repoId }),
);

export const createRepo = createAsyncAction(
  'CREATE_REPO_REQUESTED',
  'CREATE_REPO_SUCCEEDED',
  'CREATE_REPO_FAILED',
)<void, ApiRepo, AjaxError>();

export const newRepoCreated = createAction(
  'NEW_REPO_CREATED',
  resolve => (repo: ApiRepo) => resolve({ repo }),
);

export const embedSet = createAction(
  'EMBED_SET',
  resolve => (repoId: number, blobId: number) => resolve({ repoId, blobId }),
);
