import type { ImportedType } from '../types';

export type ActionCreatorDeclaration = {
  type: string
};

export const ACTION_DECLARATION = 'ACTION_DECLARATION';

export function actionDeclaration() {
  return {
    type: ACTION_DECLARATION
  };
}

export const ACTION_ARROW = 'ACTION_ARROW';

export const actionArrow = () => ({ type: ACTION_ARROW });

export const ACTION_DECLARATION_WITH_PAYLOAD =
  'ACTION_DECLARATION_WITH_PAYLOAD';

export function actionDeclarationWithPayload(name: string, value: string) {
  return {
    type: ACTION_DECLARATION_WITH_PAYLOAD,
    payload: { name, value }
  };
}

export const ACTION_ARROW_WITH_PAYLOAD = 'ACTION_ARROW_WITH_PAYLOAD';

export const actionArrowWithPayload = (name: string, value: string) => ({
  type: ACTION_ARROW_WITH_PAYLOAD,
  payload: { name, value }
});

export const ACTION_DECLARATION_WITH_META = 'ACTION_DECLARATION_WITH_META';

export function actionDeclarationWithMeta(name: string, value: string) {
  return {
    type: ACTION_DECLARATION_WITH_META,
    meta: { name, value }
  };
}

export const ACTION_ARROW_WITH_META = 'ACTION_ARROW_WITH_META';

export const actionArrowWithMeta = (name: string, value: string) => ({
  type: ACTION_ARROW_WITH_META,
  meta: { name, value }
});
