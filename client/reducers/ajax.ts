import { getType } from 'typesafe-actions';
import {
  ajaxStarted,
  ajaxFailed,
  ajaxFinished,
  searchInput,
  searchResultsSucceeded
} from '../actions';
import { RootAction } from '../util';

export type AjaxState = {
  running: boolean;
};

const defaultState: AjaxState = {
  running: false
};

export const ajaxReducer = (
  state: AjaxState = defaultState,
  action: RootAction
) => {
  switch (action.type) {
    case getType(ajaxStarted):
      return { ...state, running: true };
    case getType(ajaxFailed):
      return { ...state, running: false };
    case getType(ajaxFinished):
      return { ...state, running: false };
    case getType(searchInput):
      return { ...state, running: !!action.payload.value };
    case getType(searchResultsSucceeded):
      return { ...state, running: false };
    default:
      return state;
  }
};
