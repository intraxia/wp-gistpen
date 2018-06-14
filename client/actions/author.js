// @flow
type Author = {
    id: number,
    name: string,
    url: string,
    description: string,
    link: string,
    slug: string,
    avatar_urls: {
        [key: string]: string
    }
};

export const FETCH_AUTHOR_SUCCEEDED = 'FETCH_AUTHOR_SUCCEEDED';

export type FetchAuthorSucceeded = {
    type: typeof FETCH_AUTHOR_SUCCEEDED,
    payload: {
        author: Author
    }
};

export const fetchAuthorSucceeded = (author: Author): FetchAuthorSucceeded => ({
    type: FETCH_AUTHOR_SUCCEEDED,
    payload: { author }
});

export const FETCH_AUTHOR_FAILED = 'FETCH_AUTHOR_FAILED';

export type FetchAuthorFailedAction = {
    type: typeof FETCH_AUTHOR_FAILED,
    payload: TypeError,
    error: true
};

export const fetchAuthorFailed = (err: TypeError): FetchAuthorFailedAction => ({
    type: FETCH_AUTHOR_FAILED,
    payload: err,
    error: true
});
