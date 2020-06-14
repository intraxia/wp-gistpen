import { createAction } from 'typesafe-actions';
import { GlobalsState } from './state';

export const globalsChanged = createAction(
  'GLOBALS_CHANGED',
  resolve => (globals: Partial<GlobalsState>) => resolve(globals),
);
