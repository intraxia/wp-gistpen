import { ActionType } from 'typesafe-actions';
import * as actions from './actions';

// @todo fix this type
export type Loopable<I extends string, E> = {
  order: Array<I>;
  dict: {
    [key: string]: E;
  };
};

export type Toggle = 'on' | 'off';

export type Cursor = false | [number, number];

export type RootAction = ActionType<typeof actions>;

export type ApiLanguage = {
  ID: number;
  display_name: string;
  slug: string;
};

export type ApiBlob = {
  filename: string;
  code: string;
  language: ApiLanguage;
  ID: number;
  size?: number;
  raw_url?: string;
  edit_url?: string;
};

export type ApiRepo = {
  ID: number;
  description: string;
  status: string;
  password: string;
  gist_id: string;
  gist_url: string | null;
  sync: Toggle;
  blobs: ApiBlob[];
  rest_url: string;
  commits_url: string;
  html_url: string;
  created_at: string;
  updated_at: string;
};

export type UserApiResponse = {};
export type SearchApiResponse = {};

export type GetCommitsResponse = {};

export type ApiResponse =
  | UserApiResponse
  | SearchApiResponse
  | GetCommitsResponse;

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
  status?: JobStatus;
  runs?: Loopable<string, Run>;
};
