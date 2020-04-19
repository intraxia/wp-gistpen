import { createAction } from 'typesafe-actions';
import { Job, Run, Message } from '../reducers';

export const jobFetchStarted = createAction('JOB_FETCH_STARTED');

export const jobFetchSucceeded = createAction(
  'JOB_FETCH_SUCCEEDED',
  resolve => (response: Job) => resolve({ response }),
);

export const jobFetchFailed = createAction(
  'JOB_FETCH_FAILED',
  resolve => (slug: string, error: TypeError) => resolve({ slug, error }),
);

export const runsFetchStarted = createAction('RUNS_FETCH_STARTED');

export const runsFetchSucceeded = createAction(
  'RUNS_FETCH_SUCCEEDED',
  resolve => (response: Array<Run>) => resolve({ response }),
);

export const runsFetchFailed = createAction(
  'RUNS_FETCH_FAILED',
  resolve => (err: TypeError) => resolve(err),
);

export const messagesFetchStarted = createAction('MESSAGES_FETCH_STARTED');

export const messagesFetchSucceeded = createAction(
  'MESSAGES_FETCH_SUCCEEDED',
  // @todo extract type to API
  resolve => (response: { messages: Array<Message> }) => resolve({ response }),
);

export const messagesFetchFailed = createAction(
  'MESSAGES_FETCH_FAILED',
  resolve => (err: TypeError) => resolve(err),
);

export const jobDispatchClick = createAction(
  'JOB_DISPATCH_CLICK',
  resolve => (key: string) => resolve(undefined, { key }),
);

export const jobDispatchStarted = createAction('JOB_DISPATCH_STARTED');

export const jobDispatchSucceeded = createAction(
  'JOB_DISPATCH_SUCCEEDED',
  resolve => (response: Run) => resolve({ response }),
);

export const jobDispatchFailed = createAction(
  'JOB_DISPATCH_FAILED',
  resolve => (err: TypeError) => resolve(err),
);
