import { createAction } from 'typesafe-actions';
import { AjaxError } from '../api';
import { UserApiResponse } from '../deltas';
import { ApiRepo } from '../snippet';

export const ajaxStarted = createAction('AJAX_STARTED');

export const ajaxFinished = createAction('AJAX_FINISHED');

export const ajaxFailed = createAction(
  'AJAX_FAILED',
  resolve => (error: AjaxError) => resolve({ error }),
);

export const repoSaveSucceeded = createAction(
  'REPO_SAVE_SUCCEEDED',
  resolve => (response: ApiRepo) => resolve({ response }),
);

export const userSaveSucceeded = createAction(
  'USER_SAVE_SUCCEEDED',
  resolve => (response: UserApiResponse) => resolve({ response }),
);
