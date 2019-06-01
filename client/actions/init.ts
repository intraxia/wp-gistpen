import { createAction } from 'typesafe-actions';
import { GlobalsState } from '../reducers';

export const init = createAction(
  'INIT',
  resolve => (initial: { globals: Partial<GlobalsState> }) => resolve(initial)
);
