/* eslint-env jest */
import {
  editorIndent,
  editorMakeNewline,
  editorValueChange,
  editorCursorMove,
} from '../actions';
import { State, reducer } from '../state';

describe('reducer', () => {
  const initial: State = {
    tabs: true,
    width: 4,
    code: 'echo "Hello";\necho "world!";',
    cursor: [13, 13],
    history: {
      undo: [],
      redo: [],
    },
  };

  it('should ignore random actions', () => {
    expect(reducer(initial, { type: 'RANDOM' } as any)).toBe(initial);
  });

  it('should save the previous state when getting a new change', () => {
    const action = editorValueChange({
      code: 'echo "Hello";\necho "world!";\n',
      cursor: [14, 14],
    });
    const before = initial;
    const after = {
      tabs: true,
      width: 4,
      code: 'echo "Hello";\necho "world!";\n',
      cursor: [14, 14],
      history: {
        undo: [
          {
            code: initial.code,
            cursor: initial.cursor,
          },
        ],
        redo: [],
      },
    };

    expect(reducer(before, action)).toEqual(after);
  });

  it('should tab indent line the cursor is on', () => {
    const action = editorIndent({
      code: 'echo "Hello";\necho "world!";',
      cursor: [13, 13],
      inverse: false,
    });
    const before = initial;
    const after = {
      tabs: true,
      width: 4,
      code: 'echo "Hello";\t\necho "world!";',
      cursor: [14, 14],
      history: {
        undo: [
          {
            code: 'echo "Hello";\necho "world!";',
            cursor: [13, 13],
          },
        ],
        redo: [],
      },
    };

    expect(reducer(before, action)).toEqual(after);
  });

  it('should space indent by width line the cursor is on', () => {
    const action = editorIndent({
      code: 'echo "Hello";\necho "world!";',
      cursor: [13, 13],
      inverse: false,
    });
    const before: State = { ...initial, tabs: false };
    const after = {
      tabs: false,
      width: 4,
      code: 'echo "Hello";    \necho "world!";',
      cursor: [17, 17],
      history: {
        undo: [
          {
            code: 'echo "Hello";\necho "world!";',
            cursor: [13, 13],
          },
        ],
        redo: [],
      },
    };

    expect(reducer(before, action)).toEqual(after);
  });

  it('should space indent by width when cursor is on multiple lines', () => {
    const action = editorIndent({
      code: 'echo "Hello";\necho "world!";',
      cursor: [0, 28],
      inverse: false,
    });
    const before: State = { ...initial, tabs: false };
    const after = {
      tabs: false,
      width: 4,
      code: '    echo "Hello";\n    echo "world!";',
      cursor: [4, 36],
      history: {
        undo: [
          {
            code: 'echo "Hello";\necho "world!";',
            // This matches the previous state, not the dispatched value.
            cursor: [13, 13],
          },
        ],
        redo: [],
      },
    };

    expect(reducer(before, action)).toEqual(after);
  });

  it('should delete tab next to cursor if inverse', () => {
    const action = editorIndent({
      code: 'echo "Hello";\t\necho "world!";',
      cursor: [14, 14],
      inverse: true,
    });
    const before: State = {
      ...initial,
      code: 'echo "Hello";\t\necho "world!";',
      cursor: [14, 14],
    };
    const after = {
      tabs: true,
      width: 4,
      code: 'echo "Hello";\necho "world!";',
      cursor: [13, 13],
      history: {
        undo: [
          {
            code: 'echo "Hello";\t\necho "world!";',
            cursor: [14, 14],
          },
        ],
        redo: [],
      },
    };

    expect(reducer(before, action)).toEqual(after);
  });

  it('should delete tab from line the cursor is on if inverse', () => {
    const action = editorIndent({
      code: '\techo "Hello";\necho "world!";',
      cursor: [14, 14],
      inverse: true,
    });
    const before: State = {
      ...initial,
      code: '\techo "Hello";\necho "world!";',
      cursor: [14, 14],
    };
    const after = {
      tabs: true,
      width: 4,
      code: 'echo "Hello";\necho "world!";',
      cursor: [13, 13],
      history: {
        undo: [
          {
            code: '\techo "Hello";\necho "world!";',
            cursor: [14, 14],
          },
        ],
        redo: [],
      },
    };

    expect(reducer(before, action)).toEqual(after);
  });

  it('should delete spaces before the cursor if inverse', () => {
    const action = editorIndent({
      code: 'echo "Hello";    \necho "world!";',
      cursor: [17, 17],
      inverse: true,
    });
    const before: State = {
      ...initial,
      tabs: false,
      code: 'echo "Hello";    \necho "world!";',
      cursor: [17, 17],
    };
    const after = {
      tabs: false,
      width: 4,
      code: 'echo "Hello";\necho "world!";',
      cursor: [13, 13],
      history: {
        undo: [
          {
            code: 'echo "Hello";    \necho "world!";',
            cursor: [17, 17],
          },
        ],
        redo: [],
      },
    };

    expect(reducer(before, action)).toEqual(after);
  });

  it('should delete spaces at start of line cursor is on if inverse', () => {
    const action = editorIndent({
      code: '    echo "Hello";\necho "world!";',
      cursor: [17, 17],
      inverse: true,
    });
    const before: State = {
      ...initial,
      tabs: false,
      code: '    echo "Hello";\necho "world!";',
      cursor: [17, 17],
    };
    const after = {
      tabs: false,
      width: 4,
      code: 'echo "Hello";\necho "world!";',
      cursor: [13, 13],
      history: {
        undo: [
          {
            code: '    echo "Hello";\necho "world!";',
            cursor: [17, 17],
          },
        ],
        redo: [],
      },
    };

    expect(reducer(before, action)).toEqual(after);
  });

  it('should add newline and indentation', () => {
    const action = editorMakeNewline({
      code: '    echo "Hello";\necho "world!";',
      cursor: [17, 17],
    });
    const before: State = {
      ...initial,
      tabs: false,
      code: '    echo "Hello";\necho "world!";',
      cursor: [17, 17],
    };
    const after = {
      tabs: false,
      width: 4,
      code: '    echo "Hello";\n    \necho "world!";',
      cursor: [22, 22],
      history: {
        undo: [
          {
            code: '    echo "Hello";\necho "world!";',
            cursor: [17, 17],
          },
        ],
        redo: [],
      },
    };

    expect(reducer(before, action)).toEqual(after);
  });

  it('should remove indentation when cursor at beginning of line', () => {
    const action = editorIndent({
      code: '    echo "Hello";\necho "world!";',
      cursor: [0, 0],
      inverse: true,
    });
    const before: State = {
      ...initial,
      tabs: false,
      code: '    echo "Hello";\necho "world!";',
      cursor: [0, 0],
    };
    const after = {
      tabs: false,
      width: 4,
      code: 'echo "Hello";\necho "world!";',
      cursor: [0, 0],
      history: {
        undo: [
          {
            code: '    echo "Hello";\necho "world!";',
            cursor: [0, 0],
          },
        ],
        redo: [],
      },
    };

    expect(reducer(before, action)).toEqual(after);
  });

  it('should save the new cursor position when it moves', () => {
    const action = editorCursorMove(null);
    const before = initial;
    const after = {
      tabs: true,
      width: 4,
      code: 'echo "Hello";\necho "world!";',
      cursor: null,
      history: {
        undo: [],
        redo: [],
      },
    };

    expect(reducer(before, action)).toEqual(after);
  });
});
