import { getType } from 'typesafe-actions';
import { RootAction } from '../util';
import { init } from '../actions';

export type GlobalsState = {
  languages: { [key: string]: string };
  root: string;
  nonce: string;
  url: string;
  ace_widths: Array<number>;
  statuses: { [key: string]: string };
  themes: { [key: string]: string };
  repo?: object;
};

const defaultState: GlobalsState = {
  languages: {},
  root: '',
  nonce: '',
  url: '',
  ace_widths: [],
  statuses: {},
  themes: {}
};

export const globalsReducer = (
  state: GlobalsState = defaultState,
  action: RootAction
) => {
  switch (action.type) {
    case getType(init):
      return {
        ...state,
        ...action.payload.globals
      };
    default:
      return state;
  }
};
