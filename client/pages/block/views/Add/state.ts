import { ActionType } from 'typesafe-actions';
import { Nullable } from 'typescript-nullable';
import * as actions from './actions';
import { GlobalsState } from '../../../../reducers';

export type State = {
  view: 'choose' | 'new' | 'existing';
  repoId: Nullable<number>;
  blobId: Nullable<number>;
  description: string;
  search: string;
  saving: boolean;
  error: Nullable<TypeError>;
  globals: GlobalsState;
};

export const initialState: State = {
  repoId: null,
  blobId: null,
  view: 'choose',
  description: '',
  search: '',
  saving: false,
  error: null,
  globals: window.__GISTPEN_TINYMCE__.globals
};

export type Action = ActionType<typeof actions>;
