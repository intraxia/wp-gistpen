import { routeChange } from '../actions';
import { RootAction } from '../util';
import { getType } from 'typesafe-actions';
import { Nullable } from 'typescript-nullable';

export type RouteParts = {
  [key: string]: string | number;
};

export type Route = {
  name: string;
  parts: RouteParts;
};

export type RouteState = Nullable<Route>;

const defaultState: RouteState = null;

export const routeReducer = (
  state: RouteState = defaultState,
  action: RootAction
) => {
  switch (action.type) {
    case getType(routeChange):
      return action.payload;
    default:
      return state;
  }
};
