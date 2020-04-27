import { createAction } from 'typesafe-actions';

export const change = createAction('CHANGE', resolve => (value: string) =>
  resolve({ value }),
);

export const click = createAction('CLICK');
