import { Nullable } from 'typescript-nullable';
import { getType } from 'typesafe-actions';
import { EddyReducer } from 'brookjs';
import { repoSaveSucceeded } from '../actions';
import { RootAction } from '../util';
import { ApiRepo } from '../api';

export type RepoState = Nullable<ApiRepo>;

const defaultState: RepoState = null;

export const repoReducer: EddyReducer<RepoState, RootAction> = (
  state = defaultState,
  action
) => {
  switch (action.type) {
    case getType(repoSaveSucceeded):
      return action.payload.response;
    default:
      return state;
  }
};
