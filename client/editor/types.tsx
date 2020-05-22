import { ActionType } from 'typesafe-actions';
import * as actions from './actions';

export type PrismLib = typeof import('prismjs');

export type EditorAction = ActionType<typeof actions>;

export type Cursor = null | [number, number];

export type EditorValue = {
  code: string;
  cursor: Cursor;
};

export type EditorIndentValue = EditorValue & {
  inverse: boolean;
};

export type EditorSnapshot = {
  code: string;
  cursor: Cursor;
};

export type EditorHistory = {
  undo: EditorSnapshot[];
  redo: EditorSnapshot[];
};
