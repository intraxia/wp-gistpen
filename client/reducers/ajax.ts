import { getType } from 'typesafe-actions';
import { EddyReducer } from 'brookjs';
import { ajaxStarted, ajaxFailed, ajaxFinished } from '../actions';
import { RootAction } from '../RootAction';
import { actions as searchActions } from '../search';

export type AjaxState = {
  running: boolean;
};

const defaultState: AjaxState = {
  running: false,
};

export const ajaxReducer: EddyReducer<AjaxState, RootAction> = (
  state = defaultState,
  action,
) => {
  switch (action.type) {
    case getType(ajaxStarted):
      return { ...state, running: true };
    case getType(ajaxFailed):
      return { ...state, running: false };
    case getType(ajaxFinished):
      return { ...state, running: false };
    case getType(searchActions.searchInput):
      return { ...state, running: !!action.payload.value };
    case getType(searchActions.search.success):
      return { ...state, running: false };
    default:
      return state;
  }
};
