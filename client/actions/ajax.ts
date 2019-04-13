import { createAction } from 'typesafe-actions';
import { ApiResponse, UserApiResponse, SearchApiResponse } from '../util';
import { ApiRepo } from '../deltas';

export const ajaxStarted = createAction('AJAX_STARTED');

export const ajaxFinished = createAction(
  'AJAX_FINISHED',
  resolve => (response: ApiResponse) => resolve({ response })
);

export const ajaxFailed = createAction(
  'AJAX_FAILED',
  resolve => (error: Error) => resolve({ error })
);

export const repoSaveSucceeded = createAction(
  'REPO_SAVE_SUCCEEDED',
  resolve => (response: ApiRepo) => resolve({ response })
);

export const userSaveSucceeded = createAction(
  'USER_SAVE_SUCCEEDED',
  resolve => (response: UserApiResponse) => resolve({ response })
);

export const searchResultsSucceeded = createAction(
  'SEARCH_RESULTS_SUCCEEDED',
  resolve => (response: SearchApiResponse) => resolve({ response })
);
