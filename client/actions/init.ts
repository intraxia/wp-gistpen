import { createAction } from 'typesafe-actions';

export const init = createAction(
  'INIT',
  resolve => (initial: { globals: object }) => resolve(initial)
);
