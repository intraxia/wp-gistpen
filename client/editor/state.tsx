import { EddyReducer } from 'brookjs';
import { getType } from 'typesafe-actions';
import {
  EditorAction,
  EditorIndentValue,
  EditorValue,
  Cursor,
  EditorHistory,
} from './types';
import {
  editorValueChange,
  editorMakeNewline,
  editorIndent,
  editorTabsChange,
  editorCursorMove,
  editorWidthChange,
} from './actions';

export type State = {
  code: string;
  cursor: Cursor;
  history: EditorHistory;
  tabs: boolean;
  width: number;
};

export const initialState: State = {
  code: '',
  cursor: null,
  tabs: false,
  width: 2,
  history: {
    undo: [],
    redo: [],
  },
};
export const reducer: EddyReducer<State, EditorAction> = (
  state = initialState,
  action,
) => {
  switch (action.type) {
    case getType(editorTabsChange):
      return {
        ...state,
        tabs: action.payload.tabs,
      };
    case getType(editorWidthChange):
      return {
        ...state,
        width: action.payload.width,
      };
    case getType(editorValueChange):
      return {
        ...state,
        code: action.payload.code,
        cursor: action.payload.cursor,
        history: {
          ...state.history,
          undo: state.history.undo.concat({
            code: state.code,
            cursor: state.cursor,
          }),
        },
      };
    case getType(editorMakeNewline):
      return {
        ...state,
        ...makeNewline(action.payload),
        history: {
          ...state.history,
          undo: state.history.undo.concat({
            code: state.code,
            cursor: state.cursor,
          }),
        },
      };
    case getType(editorIndent):
      return {
        ...state,
        ...indent(action.payload, { tabs: state.tabs, width: state.width }),
        history: {
          ...state.history,
          undo: state.history.undo.concat({
            code: state.code,
            cursor: state.cursor,
          }),
        },
      };
    case getType(editorCursorMove):
      return {
        ...state,
        cursor: action.payload.cursor,
      };
    default:
      return state;
  }
};

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
    after: code.slice(se),
  };
}

type Indentation = {
  tabs: boolean;
  width: number;
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
  { tabs, width }: Indentation,
): EditorValue {
  if (!cursor) {
    return { code, cursor };
  }
  let [ss, se] = cursor;
  const { before, selection, after } = extractSections(code, ss, se);

  const befores = before.split('\n');
  const rolBefore = befores.pop() || '';
  const afters = after.split('\n');
  const rolAfter = afters.shift();
  const lines = (rolBefore + selection + rolAfter).split('\n');
  const append = tabs ? '\t' : new Array(width + 1).join(' ');

  for (let i = 0; i < lines.length; i++) {
    const isFirstLine = i === 0;
    const isFirstLineWithoutSelection = isFirstLine && ss === se;
    let line = lines[i];

    if (inverse) {
      if (tabs) {
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
        let newRolBefore = rolBefore;

        while (width) {
          if (
            isFirstLineWithoutSelection &&
            ' ' === newRolBefore.charAt(newRolBefore.length - 1)
          ) {
            newRolBefore = rolBefore.slice(0, newRolBefore.length - 1);

            if (
              !width ||
              ' ' !== newRolBefore.charAt(newRolBefore.length - 1)
            ) {
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

          width--;
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
    cursor: [ss, se],
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
    cursor: [ss, se],
  };
}
