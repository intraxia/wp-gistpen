import { getType } from 'typesafe-actions';
import { EddyReducer } from 'brookjs';
import { runsFetchSucceeded } from '../actions';
import { RootAction } from '../RootAction';
import { Run } from './jobs';

export type RunsState = {
  items: {
    [key: string]: Run;
  };
};

const defaultState: RunsState = {
  items: {},
};

export const runsReducer: EddyReducer<RunsState, RootAction> = (
  state = defaultState,
  action: RootAction,
) => {
  switch (action.type) {
    case getType(runsFetchSucceeded):
      return {
        ...state,
        items: action.payload.response.reduce(
          (items, run) => ({
            ...items,
            [run.ID]: run,
          }),
          state.items,
        ),
      };
    default:
      return state;
  }
};
