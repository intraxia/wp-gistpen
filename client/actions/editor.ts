import { createAction } from 'typesafe-actions';
import { Cursor } from '../util';
import { Toggle } from '../snippet';

export type EditorValue = {
  code: string;
  cursor: Cursor;
};

export type EditorIndentValue = EditorValue & {
  inverse: boolean;
};

export const editorThemeChange = createAction(
  'EDITOR_THEME_CHANGE',
  resolve => (value: string) => resolve({ value }),
);

export const editorTabsToggle = createAction(
  'EDITOR_TABS_TOGGLE',
  resolve => (value: Toggle) => resolve({ value }),
);

export const editorWidthChange = createAction(
  'EDITOR_WIDTH_CHANGE',
  resolve => (value: string) => resolve({ value }),
);

export const editorInvisiblesToggle = createAction(
  'EDITOR_INVISIBLES_TOGGLE',
  resolve => (value: Toggle) => resolve({ value }),
);

export const editorUpdateClick = createAction('EDITOR_UPDATE_CLICK');

export const editorAddClick = createAction('EDITOR_ADD_CLICK');

export const editorDeleteClick = createAction(
  'EDITOR_DELETE_CLICK',
  resolve => (key: string | null) => resolve(undefined, { key }),
);

export const editorDescriptionChange = createAction(
  'EDITOR_DESCRIPTION_CHANGE',
  resolve => (value: string) => resolve({ value }),
);

export const editorStatusChange = createAction(
  'EDITOR_STATUS_CHANGE',
  resolve => (value: string) => resolve({ value }),
);

export const editorSyncToggle = createAction(
  'EDITOR_SYNC_TOGGLE',
  resolve => (value: Toggle) => resolve({ value }),
);

export const editorFilenameChange = createAction(
  'EDITOR_FILENAME_CHANGE',
  resolve => (value: string, key: string | null) => resolve({ value }, { key }),
);

export const editorLanguageChange = createAction(
  'EDITOR_LANGUAGE_CHANGE',
  resolve => (value: string, key: string | null) => resolve({ value }, { key }),
);

export const editorValueChange = createAction(
  'EDITOR_VALUE_CHANGE',
  resolve => (value: EditorValue, key: string | null) =>
    resolve(value, { key }),
);

export const editorIndent = createAction(
  'EDITOR_INDENT_ACTION',
  resolve => (value: EditorIndentValue, key: string | null) =>
    resolve(value, { key }),
);

export const editorMakeComment = createAction(
  'EDITOR_MAKE_COMMENT',
  resolve => (value: EditorValue) => resolve(value),
);

export const editorMakeNewline = createAction(
  'EDITOR_MAKE_NEWLINE',
  resolve => (value: EditorValue, key: string | null) =>
    resolve(value, { key }),
);

export const editorRedo = createAction('EDITOR_REDO');

export const editorUndo = createAction('EDITOR_UNDO');

export const editorCursorMove = createAction(
  'EDITOR_CURSOR_MOVE',
  resolve => (cursor: Cursor, key: string | null) =>
    resolve({ cursor }, { key }),
);
