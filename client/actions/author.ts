import { createAction } from 'typesafe-actions';
import { Author } from '../reducers';

export const fetchAuthorSucceeded = createAction(
  'FETCH_AUTHOR_SUCCEEDED',
  resolve => (author: Author) => resolve({ author })
);

export const fetchAuthorFailed = createAction(
  'FETCH_AUTHOR_FAILED',
  resolve => (err: TypeError) => resolve(err)
);
