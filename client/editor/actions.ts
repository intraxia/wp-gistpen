import { createAction } from 'typesafe-actions';
import { Cursor, EditorIndentValue, EditorValue } from './types';

export const editorCursorMove = createAction(
  'EDITOR_CURSOR_MOVE',
  resolve => (cursor: Cursor) => resolve({ cursor }),
);

export const editorIndent = createAction(
  `EDITOR_INDENT`,
  resolve => (value: EditorIndentValue) => resolve(value),
);

export const editorMakeComment = createAction(
  'EDITOR_MAKE_COMMENT',
  resolve => (value: EditorValue) => resolve(value),
);

export const editorMakeNewline = createAction(
  'EDITOR_MAKE_NEWLINE',
  resolve => (value: EditorValue) => resolve(value),
);

export const editorValueChange = createAction(
  'EDITOR_VALUE_CHANGE',
  resolve => (value: EditorValue) => resolve(value),
);

export const editorRedo = createAction('EDITOR_REDO');

export const editorUndo = createAction('EDITOR_UNDO');

export const editorTabsChange = createAction(
  'EDITOR_TABS_CHANGE',
  resolve => (tabs: boolean) => resolve({ tabs }),
);

export const editorWidthChange = createAction(
  'EDITOR_WIDTH_CHANGE',
  resolve => (width: number) => resolve({ width }),
);

export const editorStateChange = createAction(
  'EDITOR_STATE_CHANGE',
  resolve => (code: string, cursor: Cursor) => resolve({ code, cursor }),
);
