import { createAction } from 'typesafe-actions';

type RouteParts = {
  [key: string]: string;
};

type Routes = string;

export const routeChange = createAction(
  'ROUTE_CHANGE',
  resolve => (name: Routes, parts: RouteParts = {}) => resolve({ name, parts })
);
