import { getType } from 'typesafe-actions';
import { runsFetchSucceeded } from '../actions';
import { RootAction } from '../util';
import { Run } from './jobs';

export type RunsState = {
  items: {
    [key: string]: Run;
  };
};

const defaultState: RunsState = {
  items: {}
};

export const runsReducer = (
  state: RunsState = defaultState,
  action: RootAction
) => {
  switch (action.type) {
    case getType(runsFetchSucceeded):
      return {
        ...state,
        items: action.payload.response.reduce(
          (items, run) => ({
            ...items,
            [run.ID]: run
          }),
          state.items
        )
      };
    default:
      return state;
  }
};
