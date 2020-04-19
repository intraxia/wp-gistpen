import { createAction } from 'typesafe-actions';

export const gistTokenChange = createAction(
  'GIST_TOKEN_CHANGE',
  resolve => (value: string) => resolve({ value }),
);
