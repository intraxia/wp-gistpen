import { createAction, createAsyncAction } from 'typesafe-actions';
import { ApiRepo } from '../../../../api';

export const addToNewBtnClick = createAction('ADD_TO_NEW_BTN_CLICK');

export const addToExistingBtnClick = createAction('ADD_TO_EXISTING_BTN_CLICK');

export const descriptionChange = createAction(
  'DESCRIPTION_CHANGE',
  resolve => (description: string) => resolve({ description })
);

export const saveNewBtnClick = createAction('SAVE_NEW_BTN_CLICK');

export const backClick = createAction('BACK_CLICK');

export const searchChange = createAction(
  'SEARCH_CHANGE',
  resolve => (search: string) => resolve({ search })
);

export const createRepo = createAsyncAction(
  'CREATE_REPO_REQUESTED',
  'CREATE_REPO_SUCCEEDED',
  'CREATE_REPO_FAILED'
)<void, ApiRepo, TypeError>();
