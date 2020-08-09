import { getType } from 'typesafe-actions';
import { Nullable } from 'typescript-nullable';
import { EddyReducer } from 'brookjs';
import { routeChange } from '../actions';
import { RootAction } from '../RootAction';

export type RouteParts = {
  [key: string]: string;
};

export type Route = {
  name: string;
  parts: RouteParts;
};

export type RouteState = Nullable<Route>;

const defaultState: RouteState = null;

export const routeReducer: EddyReducer<RouteState, RootAction> = (
  state = defaultState,
  action,
) => {
  switch (action.type) {
    case getType(routeChange):
      return action.payload;
    default:
      return state;
  }
};
