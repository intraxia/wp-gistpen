import { createAction } from 'typesafe-actions';
import { ValidationError, ApiRepo } from '../api';
import { UserApiResponse } from '../deltas';

export const ajaxStarted = createAction('AJAX_STARTED');

export const ajaxFinished = createAction('AJAX_FINISHED');

export const ajaxFailed = createAction(
  'AJAX_FAILED',
  resolve => (error: ValidationError) => resolve({ error }),
);

export const repoSaveSucceeded = createAction(
  'REPO_SAVE_SUCCEEDED',
  resolve => (response: ApiRepo) => resolve({ response }),
);

export const userSaveSucceeded = createAction(
  'USER_SAVE_SUCCEEDED',
  resolve => (response: UserApiResponse) => resolve({ response }),
);
