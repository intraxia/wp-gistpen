import { getType } from 'typesafe-actions';
import { jobFetchSucceeded } from '../actions';
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

export type JobsState = {
  [key: string]: Job;
};

const defaultState: JobsState = {};

export const jobsReducer = (
  state: JobsState = defaultState,
  action: RootAction
) => {
  switch (action.type) {
    case getType(jobFetchSucceeded):
      return {
        ...state,
        [action.payload.response.slug]: action.payload.response
      };
    default:
      return state;
  }
};
