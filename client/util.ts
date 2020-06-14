import { ActionType } from 'typesafe-actions';

// @todo fix this type
export type Loopable<I extends string, E> = {
  order: Array<I>;
  dict: {
    [key: string]: E;
  };
};

export type Cursor = false | [number, number];

export type RootAction = ActionType<
  typeof import('./actions') &
    typeof import('./search').actions &
    typeof import('./block').actions &
    typeof import('./globals/actions') &
    typeof import('./snippet/actions')
>;
