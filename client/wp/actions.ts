import { createAction } from 'typesafe-actions';

export const change = createAction('CHANGE', resolve => (value: string) =>
  resolve({ value }),
);

export const checked = createAction(
  'CHECKED',
  resolve => (isChecked: boolean) => resolve({ isChecked }),
);

export const click = createAction('CLICK');
