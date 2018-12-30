import { getType } from 'typesafe-actions';
import { RootAction } from '../util';
import {
  themeChange,
  lineNumbersChange,
  showInvisiblesChange
} from '../actions';

export type PrismState = {
  theme: string;
  'line-numbers': boolean;
  'show-invisibles': boolean;
};

const defaultState: PrismState = {
  theme: 'default',
  'line-numbers': false,
  'show-invisibles': false
};

export const prismReducer = (
  state: PrismState = defaultState,
  action: RootAction
) => {
  switch (action.type) {
    case getType(themeChange):
      return {
        ...state,
        theme: action.payload.value
      };
    case getType(lineNumbersChange):
      return {
        ...state,
        'line-numbers': action.payload.value
      };
    case getType(showInvisiblesChange):
      return {
        ...state,
        'show-invisibles': action.payload.value
      };
    default:
      return state;
  }
};
