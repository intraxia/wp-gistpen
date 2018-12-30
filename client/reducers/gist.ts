import { getType } from 'typesafe-actions';
import { RootAction } from '../util';
import { gistTokenChange } from '../actions';

export type GistState = {
  token: string;
};

const defaultState: GistState = {
  token: ''
};

export const gistReducer = (
  state: GistState = defaultState,
  action: RootAction
) => {
  switch (action.type) {
    case getType(gistTokenChange):
      return {
        ...state,
        token: action.payload.value
      };
    default:
      return state;
  }
};
