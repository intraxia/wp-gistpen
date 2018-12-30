import { getType } from 'typesafe-actions';
import {
  editorThemeChange,
  editorTabsToggle,
  editorWidthChange,
  editorInvisiblesToggle,
  editorDescriptionChange,
  editorStatusChange,
  editorSyncToggle,
  editorAddClick,
  editorDeleteClick,
  editorCursorMove,
  editorFilenameChange,
  editorLanguageChange,
  editorIndent,
  editorMakeNewline,
  EditorValue,
  EditorIndentValue,
  editorValueChange,
  repoSaveSucceeded
} from '../actions';
import { RootAction, Cursor, Toggle } from '../util';

export type EditorSnapshot = {
  code: string;
  cursor: Cursor;
};

export type EditorHistory = {
  undo: Array<EditorSnapshot>;
  redo: Array<EditorSnapshot>;
};

export type EditorInstance = {
  key?: string;
  filename: string;
  code: string;
  language: string;
  cursor: Cursor;
  history: EditorHistory;
};

export type EditorState = {
  description: string;
  status: string;
  password: string;
  gist_id: string;
  sync: Toggle;
  instances: Array<EditorInstance>;
  width: string;
  theme: string;
  invisibles: Toggle;
  tabs: Toggle;
};

const defaultInstance: EditorInstance = {
  filename: '',
  code: '\n',
  language: 'plaintext',
  cursor: false,
  history: {
    undo: [],
    redo: []
  }
};

const defaultState: EditorState = {
  theme: 'default',
  tabs: 'off',
  width: '4',
  invisibles: 'off',
  description: '',
  status: 'draft',
  password: '',
  gist_id: '',
  sync: 'off',
  instances: [{ ...defaultInstance, key: 'new0' }]
};

export const editorReducer = (
  state: EditorState = defaultState,
  action: RootAction
) => {
  switch (action.type) {
    case getType(editorThemeChange):
      return {
        ...state,
        theme: action.payload.value
      };
    case getType(editorTabsToggle):
      return {
        ...state,
        tabs: action.payload.value
      };
    case getType(editorWidthChange):
      return {
        ...state,
        width: action.payload.value
      };
    case getType(editorInvisiblesToggle):
      return {
        ...state,
        invisibles: action.payload.value
      };
    case getType(editorDescriptionChange):
      return {
        ...state,
        description: action.payload.value
      };
    case getType(editorStatusChange):
      return {
        ...state,
        status: action.payload.value
      };
    case getType(editorSyncToggle):
      return {
        ...state,
        sync: action.payload.value
      };
    case getType(editorAddClick):
      return {
        ...state,
        instances: [
          ...state.instances,
          {
            ...defaultInstance,
            key: createUniqueKey(state.instances)
          }
        ]
      };
    case getType(editorDeleteClick):
      return {
        ...state,
        instances: rejectWithKey(action.meta.key, state.instances)
      };
    case getType(editorCursorMove):
      return mapInstanceWithKey(state, action.meta.key, instance => ({
        ...instance,
        cursor: action.payload.cursor
      }));
    case getType(editorFilenameChange):
      return mapInstanceWithKey(state, action.meta.key, instance => ({
        ...instance,
        filename: action.payload.value
      }));
    case getType(editorLanguageChange):
      return mapInstanceWithKey(state, action.meta.key, instance => ({
        ...instance,
        language: action.payload.value
      }));
    case getType(editorIndent):
      return mapInstanceWithKey(state, action.meta.key, instance => ({
        ...instance,
        ...indent(action.payload, { tabs: state.tabs, width: state.width }),
        history: {
          ...instance.history,
          undo: instance.history.undo.concat({
            code: instance.code,
            cursor: instance.cursor
          })
        }
      }));
    case getType(editorMakeNewline):
      return mapInstanceWithKey(state, action.meta.key, instance => ({
        ...instance,
        ...makeNewline(action.payload),
        history: {
          ...instance.history,
          undo: instance.history.undo.concat({
            code: instance.code,
            cursor: instance.cursor
          })
        }
      }));
    case getType(editorValueChange):
      return mapInstanceWithKey(state, action.meta.key, instance => ({
        ...instance,
        code: action.payload.code,
        cursor: action.payload.cursor,
        history: {
          ...instance.history,
          undo: instance.history.undo.concat({
            code: instance.code,
            cursor: instance.cursor
          })
        }
      }));
    case getType(repoSaveSucceeded):
      const { response: repo } = action.payload;
      return {
        ...state,
        description: repo.description,
        status: repo.status,
        password: repo.password,
        gist_id: repo.gist_id,
        sync: repo.sync,
        instances: repo.blobs.map(blob => ({
          ...defaultInstance,
          key: blob.ID != null ? String(blob.ID) : '',
          filename: blob.filename,
          code: blob.code,
          language:
            typeof blob.language === 'string'
              ? blob.language
              : blob.language.slug
        }))
      };
    default:
      return state;
  }
};

/**
 * Returns an updated array with the instance matching the provided key removed.
 *
 * @param {string} key - Key to remove.
 * @param {Instance[]} instances - Current instances
 * @returns {Instance[]} Update instances.
 */
function rejectWithKey(
  key: string,
  instances: Array<EditorInstance>
): Array<EditorInstance> {
  return instances.filter((instance: EditorInstance) => key !== instance.key);
}

/**
 * Modify a single instance by key.
 *
 * @param {Object} state - Current state.
 * @param {string} key - Instance key to modify.
 * @param {Function} fn - Function to call
 * @returns {Object} New State.
 */
function mapInstanceWithKey(
  state: EditorState,
  key: string,
  fn: (i: EditorInstance) => EditorInstance
): EditorState {
  return {
    ...state,
    instances: state.instances.map((instance: EditorInstance) =>
      instance.key !== key ? instance : fn(instance)
    )
  };
}

type Section = {
  before: string;
  selection: string;
  after: string;
};

/**
 * Extract code sections based on selection start & end.
 *
 * @param {string} code - Current code in editor.
 * @param {number} ss - Selection start.
 * @param {number} se - Selection end.s
 * @returns {Section} Code section.
 */
function extractSections(code: string, ss: number, se: number): Section {
  return {
    before: code.slice(0, ss),
    selection: code.slice(ss, se),
    after: code.slice(se)
  };
}

type Indentation = {
  tabs: Toggle;
  width: string;
};

/**
 * Update the code and cursor position for indentation.
 *
 * @param {string} code - Current code in the editor.
 * @param {Cursor} cursor - Cursor position.
 * @param {boolean} inverse - Whether the indentation should be inverse.
 * @param {string} tabs - Whether tabs are "on" or "off".
 * @param {string} width - Width of tabs.
 * @returns {{code: string, cursor: [number, number]}} New code and cursor position.
 */
function indent(
  { code, cursor, inverse }: EditorIndentValue,
  { tabs, width }: Indentation
): EditorValue {
  if (!cursor) {
    return { code, cursor };
  }
  let [ss, se] = cursor;
  const { before, selection, after } = extractSections(code, ss, se);

  const w = parseInt(width, 10);
  const befores = before.split('\n');
  const rolBefore = befores.pop() || '';
  const afters = after.split('\n');
  const rolAfter = afters.shift();
  const lines = (rolBefore + selection + rolAfter).split('\n');
  const tabsEnabled = tabs === 'on';
  const append = tabsEnabled ? '\t' : new Array(w + 1).join(' ');

  for (let i = 0; i < lines.length; i++) {
    const isFirstLine = i === 0;
    const isFirstLineWithoutSelection = isFirstLine && ss === se;
    let line = lines[i];

    if (inverse) {
      if (tabsEnabled) {
        if (isFirstLineWithoutSelection && rolBefore.endsWith('\t')) {
          line =
            rolBefore.slice(0, rolBefore.length - 1) +
            line.replace(rolBefore, '');
        } else if (line.startsWith('\t')) {
          line = line.replace('\t', '');
        } else {
          break;
        }

        ss && ss--;
        se && se--;
      } else {
        let w = parseInt(width, 10);
        let newRolBefore = rolBefore;

        while (w) {
          if (
            isFirstLineWithoutSelection &&
            ' ' === newRolBefore.charAt(newRolBefore.length - 1)
          ) {
            newRolBefore = rolBefore.slice(0, newRolBefore.length - 1);

            if (!w || ' ' !== newRolBefore.charAt(newRolBefore.length - 1)) {
              ss && ss--;
              se && se--;
              line = line.replace(rolBefore, newRolBefore);
              break;
            }
          } else {
            if (!line.startsWith(' ')) {
              break;
            }
            line = line.replace(' ', '');
          }

          w--;
          ss && ss--;
          se && se--;
        }
      }
    } else {
      // If the cursor isn't selection anything on the line,
      // and the line is more than spaces or tabs to the left,
      // then we should insert the append at the cursor location.
      if (isFirstLineWithoutSelection && line.replace(/\s/g, '').length) {
        line = rolBefore + line.replace(rolBefore, append);
      } else {
        line = append + line;
      }

      if (isFirstLine) {
        ss += append.length;
      }

      se += append.length;
    }

    lines[i] = line;
  }

  return {
    code: [...befores, ...lines, ...afters].join('\n'),
    cursor: [ss, se]
  };
}

/**
 * Update the code and cursor position for newline.
 *
 * @param {string} code - Current code in the editor.
 * @param {Cursor} cursor - Cursor definition.
 * @returns {{code: string, cursor: [number, number]}} New code and cursor position.
 */
function makeNewline({ code, cursor }: EditorValue): EditorValue {
  if (!cursor) {
    return { code, cursor };
  }

  let [ss, se] = cursor;
  let { before, after } = extractSections(code, ss, se);

  const lf = before.lastIndexOf('\n') + 1;
  const indent = (before.slice(lf).match(/^\s+/) || [''])[0];

  before += '\n' + indent;

  ss += indent.length + 1;
  se = ss;

  return {
    code: before + after,
    cursor: [ss, se]
  };
}

/**
 * Creates a new unique key for the set of instances.
 *
 * @param {Instance[]} instances - Array of instances.
 * @returns {string} New unique key.
 */
function createUniqueKey(instances: Array<EditorInstance>): string {
  const keys = instances.map(({ key }) => key);

  let id = 0;

  while (true) {
    const key = 'new' + id;

    if (keys.indexOf(key) === -1) {
      return key;
    }

    id++;
  }
}
