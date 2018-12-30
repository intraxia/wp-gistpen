import { createAction } from 'typesafe-actions';
import { GetCommitsResponse } from '../util';

export const commitsFetchStarted = createAction('COMMITS_FETCH_STARTED');

export const commitsFetchSucceeded = createAction(
  'COMMITS_FETCH_SUCCEEDED',
  resolve => (response: GetCommitsResponse) => resolve({ response })
);

export const commitsFetchFailed = createAction('COMMITS_FETCH_FAILED');

export const commitClick = createAction(
  'COMMIT_CLICK',
  resolve => (key: string | number) => resolve(undefined, { key })
);
