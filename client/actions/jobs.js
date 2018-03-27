// @flow
import type { Run, JobDispatchClickAction, JobDispatchStarted,
    JobDispatchSucceeded, JobDispatchFailed } from '../types';
export const JOB_FETCH_STARTED = 'JOB_FETCH_STARTED';
export const JOB_FETCH_SUCCEEDED = 'JOB_FETCH_SUCCEEDED';
export const JOB_FETCH_FAILED = 'JOB_FETCH_FAILED';

export const RUNS_FETCH_STARTED = 'RUNS_FETCH_STARTED';
export const RUNS_FETCH_SUCCEEDED = 'RUNS_FETCH_SUCCEEDED';
export const RUNS_FETCH_FAILED = 'RUNS_FETCH_FAILED';

export const MESSAGES_FETCH_STARTED = 'MESSAGES_FETCH_STARTED';
export const MESSAGES_FETCH_SUCCEEDED = 'MESSAGES_FETCH_SUCCEEDED';
export const MESSAGES_FETCH_FAILED = 'MESSAGES_FETCH_FAILED';

export const JOB_DISPATCH_CLICK = 'JOB_DISPATCH_CLICK';

export function jobDispatchClick(): JobDispatchClickAction {
    return { type: JOB_DISPATCH_CLICK };
}

export const JOB_DISPATCH_STARTED = 'JOB_DISPATCH_STARTED';

export function jobDispatchStarted(): JobDispatchStarted {
    return { type: JOB_DISPATCH_STARTED };
}

export const JOB_DISPATCH_SUCCEEDED = 'JOB_DISPATCH_SUCCEEDED';

export function jobDispatchSucceeded(response: Run): JobDispatchSucceeded {
    return {
        type: JOB_DISPATCH_SUCCEEDED,
        payload: { response }
    };
}

export const JOB_DISPATCH_FAILED = 'JOB_DISPATCH_FAILED';

export function jobDispatchFailed(err: TypeError): JobDispatchFailed {
    return {
        type: JOB_DISPATCH_FAILED,
        payload: err,
        error: true
    };
}
