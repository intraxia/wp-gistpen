// @flow
import type { JobStartClickAction } from '../type';
export const JOB_FETCH_STARTED = 'JOB_FETCH_STARTED';
export const JOB_FETCH_SUCCEEDED = 'JOB_FETCH_SUCCEEDED';
export const JOB_FETCH_FAILED = 'JOB_FETCH_FAILED';

export const RUNS_FETCH_STARTED = 'RUNS_FETCH_STARTED';
export const RUNS_FETCH_SUCCEEDED = 'RUNS_FETCH_SUCCEEDED';
export const RUNS_FETCH_FAILED = 'RUNS_FETCH_FAILED';

export const MESSAGES_FETCH_STARTED = 'MESSAGES_FETCH_STARTED';
export const MESSAGES_FETCH_SUCCEEDED = 'MESSAGES_FETCH_SUCCEEDED';
export const MESSAGES_FETCH_FAILED = 'MESSAGES_FETCH_FAILED';

export const JOB_START_CLICK = 'JOB_START_CLICK';

export function jobStartClick() : JobStartClickAction {
    return { type: JOB_START_CLICK };
}
