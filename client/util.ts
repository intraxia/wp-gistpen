import { ActionType } from 'typesafe-actions';
import * as actions from './actions/init';

export type RootAction = ActionType<typeof actions>;
