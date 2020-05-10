import { createAction } from 'typesafe-actions';
import { GlobalsState } from '../globals';

export const init = createAction(
  'INIT',
  resolve => (initial: { globals: Partial<GlobalsState> }) => resolve(initial),
);
