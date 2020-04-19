import { createAction } from 'typesafe-actions';

export const searchInput = createAction(
  'SEARCH_INPUT',
  resolve => (value: string) => resolve({ value }),
);

export const searchResultSelectionChange = createAction(
  'SEARCH_RESULT_SELECTION_CHANGE',
  resolve => (selection: string) => resolve({ selection }),
);
