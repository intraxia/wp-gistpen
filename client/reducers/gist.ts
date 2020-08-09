import { getType } from 'typesafe-actions';
import { EddyReducer } from 'brookjs';
import { RootAction } from '../RootAction';
import { gistTokenChange } from '../actions';

export type GistState = {
  token: string;
};

const defaultState: GistState = {
  token: '',
};

export const gistReducer: EddyReducer<GistState, RootAction> = (
  state = defaultState,
  action,
) => {
  switch (action.type) {
    case getType(gistTokenChange):
      return {
        ...state,
        token: action.payload.value,
      };
    default:
      return state;
  }
};
