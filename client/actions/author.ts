import { createAction } from 'typesafe-actions';

type Author = {
  id: number;
  name: string;
  url: string;
  description: string;
  link: string;
  slug: string;
  avatar_urls: {
    [key: string]: string;
  };
};

export const fetchAuthorSucceeded = createAction(
  'FETCH_AUTHOR_SUCCEEDED',
  resolve => (author: Author) => resolve({ author })
);

export const fetchAuthorFailed = createAction(
  'FETCH_AUTHOR_FAILED',
  resolve => (err: TypeError) => resolve(err)
);
