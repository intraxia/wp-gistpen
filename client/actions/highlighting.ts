import { createAction } from 'typesafe-actions';

export const themeChange = createAction(
  'THEME_CHANGE',
  resolve => (value: string) => resolve({ value })
);

export const lineNumbersChange = createAction(
  'LINE_NUMBERS_CHANGE',
  resolve => (value: boolean) => resolve({ value })
);

export const showInvisiblesChange = createAction(
  'SHOW_INVISIBLES_CHANGE',
  resolve => (value: boolean) => resolve({ value })
);
