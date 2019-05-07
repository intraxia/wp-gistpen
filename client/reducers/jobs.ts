import { getType } from 'typesafe-actions';
import { jobFetchSucceeded, jobFetchFailed } from '../actions';
import { RootAction, Loopable } from '../util';

export type MessageLevel = 'error' | 'warning' | 'success' | 'info' | 'debug';

export type Message = {
  ID: string;
  run_id: string;
  text: string;
  level: MessageLevel;
  logged_at: string;
};

export type RunStatus =
  | 'scheduled'
  | 'running'
  | 'paused'
  | 'finished'
  | 'error';

export type Run = {
  ID: string;
  job: string;
  status: RunStatus;
  scheduled_at: string;
  started_at: string | null;
  finished_at: string | null;
  rest_url: string;
  job_url: string;
  console_url: string;
  messages?: Loopable<string, Message>;
};

export type JobStatus = 'idle' | 'processing';

export type Job = {
  name: string;
  slug: string;
  description: string;
  rest_url: string;
  runs_url: string;
  status: JobStatus;
  runs?: Loopable<string, Run>;
};

export type JobError = { result: 'error'; error: Error };
export type JobSuccess = { result: 'success'; response: Job };
export type JobResult = JobError | JobSuccess;

export type JobsState = {
  [key: string]: JobResult;
};

const defaultState: JobsState = {};

export const jobsReducer = (
  state: JobsState = defaultState,
  action: RootAction
): JobsState => {
  switch (action.type) {
    case getType(jobFetchSucceeded):
      return {
        ...state,
        [action.payload.response.slug]: {
          result: 'success',
          response: action.payload.response
        }
      };
    case getType(jobFetchFailed):
      return {
        ...state,
        [action.payload.slug]: {
          result: 'error',
          error: action.payload.error
        }
      };
    default:
      return state;
  }
};
