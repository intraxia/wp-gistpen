import { ActionType } from 'typesafe-actions';

export type RootAction = ActionType<
  typeof import('./actions') &
    typeof import('./search').actions &
    typeof import('./block').actions &
    typeof import('./globals/actions') &
    typeof import('./snippet/actions') &
    typeof import('./editor/actions') &
    typeof import('./settings/actions') &
    typeof import('./me/actions')
>;
