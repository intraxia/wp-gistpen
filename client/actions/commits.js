// @flow

export const COMMITS_FETCH_SUCCEEDED = 'COMMITS_FETCH_SUCCEEDED';
export const COMMITS_FETCH_STARTED = 'COMMITS_FETCH_STARTED';
export const COMMITS_FETCH_FAILED = 'COMMITS_FETCH_FAILED';

export const COMMIT_CLICK = 'COMMIT_CLICK';

export type CommitClickAction = {
    type: typeof COMMIT_CLICK,
    meta: {
        key: string | number
    }
};

export const commitClick = (key: string | number): CommitClickAction => ({
    type: COMMIT_CLICK,
    meta: { key }
});
