import { createAction } from 'typesafe-actions';
import { AjaxError } from '../ajax';
import { SearchApiResponse, UserApiResponse } from '../deltas';
import { ApiRepo } from '../api';

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

export const searchResultsSucceeded = createAction(
  'SEARCH_RESULTS_SUCCEEDED',
  resolve => (response: SearchApiResponse) => resolve({ response }),
);

export const searchsResultsFailed = createAction(
  'SEARCH_RESULTS_FAILED',
  resolve => (err: AjaxError) => resolve(err),
);
