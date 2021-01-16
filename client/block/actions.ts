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

export const editFilenameChange = createAction(
  'EDIT_FILENAME_CHANGE',
  resolve => (value: string) => resolve({ value }),
);

export const editLanguageChange = createAction(
  'EDIT_LANGUAGE_CHANGE',
  resolve => (value: string) => resolve({ value }),
);

export const editThemeChange = createAction(
  'EDIT_THEME_CHANGE',
  resolve => (value: string) => resolve({ value }),
);

export const editWidthChange = createAction(
  'EDIT_WIDTH_CHANGE',
  resolve => (value: number) => resolve({ value }),
);

export const editTabsChange = createAction(
  'EDIT_TABS_CHANGE',
  resolve => (checked: boolean) => resolve({ checked }),
);

export const editShowInvisiblesChange = createAction(
  'EDIT_SHOW_INVISIBLES_CHANGE',
  resolve => (checked: boolean) => resolve({ checked }),
);

export const editLineNumbersChange = createAction(
  'EDIT_LINE_NUMBERS_CHANGE',
  resolve => (checked: boolean) => resolve({ checked }),
);

export const saveSnippetClick = createAction('SAVE_SNIPPET_CLICK');

export const saveEditorClick = createAction('SAVE_EDITOR_CLICK');

export const saveSiteClick = createAction('SAVE_SITE_CLICK');

export const embedChanged = createAction(
  'EMBED_CHANGED',
  resolve => (repoId: number, blobId: number) => resolve({ repoId, blobId }),
);

export const appendBlob = createAsyncAction(
  'APPEND_BLOB_REQUESTED',
  'APPEND_BLOB_SUCCEEDED',
  'APPEND_BLOB_FAILED',
)<number, { repoId: number; blob: ApiBlob }, AjaxError>();

export const saveBlob = createAsyncAction(
  'SAVE_BLOB_REQUESTED',
  'SAVE_BLOB_SUCCEEDED',
  'SAVE_BLOB_FAILED',
)<void, void, AjaxError>();

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

export const highlightChange = createAction(
  'HIGHLIGHT_CHANGE',
  resolve => (highlight: string) => resolve({ highlight }),
);

export const offsetChange = createAction(
  'OFFSET_CHANGE',
  resolve => (offset: number) => resolve({ offset }),
);
