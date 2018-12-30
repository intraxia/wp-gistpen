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
