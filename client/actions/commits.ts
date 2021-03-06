import { createAction } from 'typesafe-actions';
import { ApiCommits } from '../deltas';
import { AjaxError } from '../api';

export const commitsFetchStarted = createAction('COMMITS_FETCH_STARTED');

export const commitsFetchSucceeded = createAction(
  'COMMITS_FETCH_SUCCEEDED',
  resolve => (response: ApiCommits) => resolve({ response }),
);

export const commitsFetchFailed = createAction(
  'COMMITS_FETCH_FAILED',
  resolve => (err: AjaxError) => resolve(err),
);

export const commitClick = createAction(
  'COMMIT_CLICK',
  resolve => (key: string | number) => resolve(undefined, { key }),
);
