import { ActionType } from 'typesafe-actions';
import * as t from 'io-ts';

// @todo fix this type
export type Loopable<I extends string, E> = {
  order: Array<I>;
  dict: {
    [key: string]: E;
  };
};

export const toggle = t.union([t.literal('on'), t.literal('off')]);

export type Toggle = t.TypeOf<typeof toggle>;

export type Cursor = false | [number, number];

export type RootAction = ActionType<
  typeof import('./actions') &
    typeof import('./search').actions &
    typeof import('./block').actions
>;
