import { createAction } from 'typesafe-actions';
export const actionDeclaration = createAction('ACTION_DECLARATION');
export const actionArrow = createAction('ACTION_ARROW');

export const actionDeclarationWithPayload = createAction(
  'ACTION_DECLARATION_WITH_PAYLOAD',
  resolve => (name: string, value: string) => resolve({ name, value })
);

export const actionArrowWithPayload = createAction(
  'ACTION_ARROW_WITH_PAYLOAD',
  resolve => (name: string, value: string) => resolve({ name, value })
);

export const actionDeclarationWithMeta = createAction(
  'ACTION_DECLARATION_WITH_META',
  resolve => (name: string, value: string) =>
    resolve(undefined, { name, value })
);

export const actionArrowWithMeta = createAction(
  'ACTION_ARROW_WITH_META',
  resolve => (name: string, value: string) =>
    resolve(undefined, { name, value })
);
