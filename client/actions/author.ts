import { createAction } from 'typesafe-actions';
import { Author } from '../reducers';
import { AjaxError } from '../api';

export const fetchAuthorSucceeded = createAction(
  'FETCH_AUTHOR_SUCCEEDED',
  resolve => (author: Author) => resolve({ author }),
);

export const fetchAuthorFailed = createAction(
  'FETCH_AUTHOR_FAILED',
  resolve => (err: AjaxError) => resolve(err),
);
