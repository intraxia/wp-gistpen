import * as t from 'io-ts';
import { Toggle } from '../api';

export const ApiLanguage = t.type({
  ID: t.union([t.number, t.null]),
  display_name: t.string,
  slug: t.string,
});

export type ApiLanguage = t.TypeOf<typeof ApiLanguage>;

export const ApiBlob = t.type({
  filename: t.string,
  code: t.string,
  language: ApiLanguage,
  ID: t.number,
  size: t.number,
  raw_url: t.string,
  edit_url: t.union([t.null, t.string]),
});

export type ApiBlob = t.TypeOf<typeof ApiBlob>;

export const ApiRepo = t.type({
  ID: t.number,
  description: t.string,
  status: t.string,
  password: t.string,
  gist_id: t.string,
  gist_url: t.union([t.string, t.null]),
  sync: Toggle,
  blobs: t.array(ApiBlob),
  rest_url: t.string,
  commits_url: t.string,
  html_url: t.string,
  created_at: t.string,
  updated_at: t.string,
});

export type ApiRepo = t.TypeOf<typeof ApiRepo>;
