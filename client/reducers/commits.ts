import { Nullable } from 'typescript-nullable';
import { getType } from 'typesafe-actions';
import { commitsFetchSucceeded, commitClick } from '../actions';
import { RootAction } from '../util';

// @todo flesh out & dedupe
export type BlobState = {
  language: {
    slug: string;
  };
};

export type Commit = {
  ID: number;
  description: string;
  committed_at: string;
  author: string;
  states: Array<BlobState>;
};

export type CommitsState = {
  instances: Array<Commit>;
  selected: Nullable<number>;
};

const defaultState: CommitsState = {
  instances: [],
  selected: null
};

export const commitsReducer = (
  state: CommitsState = defaultState,
  action: RootAction
) => {
  switch (action.type) {
    case getType(commitsFetchSucceeded):
      return {
        ...state,
        instances: action.payload.response
      };
    case getType(commitClick):
      return {
        ...state,
        selected:
          (typeof action.meta.key === 'string'
            ? parseInt(action.meta.key, 10)
            : action.meta.key) || null
      };
    default:
      return state;
  }
};
