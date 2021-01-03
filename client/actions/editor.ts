import { createAction } from 'typesafe-actions';
import { Toggle } from '../api';
import { Cursor } from '../editor/types';

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

export const editorInvisiblesToggle = createAction(
  'EDITOR_INVISIBLES_TOGGLE',
  resolve => (value: Toggle) => resolve({ value }),
);

export const editorUpdateClick = createAction('EDITOR_UPDATE_CLICK');

export const editorAddClick = createAction('EDITOR_ADD_CLICK');

export const editorDeleteClickWithKey = createAction(
  'EDITOR_DELETE_CLICK_WITH_KEY',
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

export const editorFilenameChangeWithKey = createAction(
  'EDITOR_FILENAME_CHANGE_WITH_KEY',
  resolve => (value: string, key: string | null) => resolve({ value }, { key }),
);

export const editorLanguageChangeWithKey = createAction(
  'EDITOR_LANGUAGE_CHANGE_WITH_KEY',
  resolve => (value: string, key: string | null) => resolve({ value }, { key }),
);

export const editorValueChangeWithKey = createAction(
  'EDITOR_VALUE_CHANGE_WITH_KEY',
  resolve => (value: EditorValue, key: string | null) =>
    resolve(value, { key }),
);

export const editorIndentWithKey = createAction(
  'EDITOR_INDENT_ACTION_WITH_KEY',
  resolve => (value: EditorIndentValue, key: string | null) =>
    resolve(value, { key }),
);

export const editorMakeComment = createAction(
  'EDITOR_MAKE_COMMENT',
  resolve => (value: EditorValue) => resolve(value),
);

export const editorMakeNewlineWithKey = createAction(
  'EDITOR_MAKE_NEWLINE_WITH_KEY',
  resolve => (value: EditorValue, key: string | null) =>
    resolve(value, { key }),
);

export const editorRedo = createAction('EDITOR_REDO');

export const editorUndo = createAction('EDITOR_UNDO');

export const editorCursorMoveWithKey = createAction(
  'EDITOR_CURSOR_MOVE_WITH_KEY',
  resolve => (cursor: Cursor, key: string | null) =>
    resolve({ cursor }, { key }),
);
