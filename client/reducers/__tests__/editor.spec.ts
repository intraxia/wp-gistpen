/* eslint-env jest */
import { editorIndentWithKey, editorMakeNewlineWithKey } from '../../actions';
import { editorReducer, EditorState } from '../editor';

describe('editorReducer', () => {
  const initial: EditorState = {
    errors: [],
    theme: 'default',
    tabs: 'on',
    width: '4',
    invisibles: 'off',
    description: 'PHP Code',
    status: 'publish',
    password: '',
    gist_id: '',
    sync: 'off',
    instances: [
      {
        key: '1',
        filename: 'file.php',
        code: 'echo "Hello";\necho "world!";',
        cursor: [13, 13],
        language: 'php',
        history: {
          undo: [],
          redo: [],
        },
      },
    ],
  };

  it('should tab indent line the cursor is on', () => {
    const action = editorIndentWithKey(
      {
        code: 'echo "Hello";\necho "world!";',
        cursor: [13, 13],
        inverse: false,
      },
      '1',
    );
    const before = initial;
    const after = {
      errors: [],
      theme: 'default',
      tabs: 'on',
      width: '4',
      invisibles: 'off',
      description: 'PHP Code',
      status: 'publish',
      password: '',
      gist_id: '',
      sync: 'off',
      instances: [
        {
          key: '1',
          filename: 'file.php',
          code: 'echo "Hello";\t\necho "world!";',
          cursor: [14, 14],
          language: 'php',
          history: {
            undo: [
              {
                code: 'echo "Hello";\necho "world!";',
                cursor: [13, 13],
              },
            ],
            redo: [],
          },
        },
      ],
    };

    expect(editorReducer(before, action)).toEqual(after);
  });

  it('should space indent by width line the cursor is on', () => {
    const action = editorIndentWithKey(
      {
        code: 'echo "Hello";\necho "world!";',
        cursor: [13, 13],
        inverse: false,
      },
      '1',
    );
    const before: EditorState = { ...initial, tabs: 'off' };
    const after = {
      errors: [],
      theme: 'default',
      tabs: 'off',
      width: '4',
      invisibles: 'off',
      description: 'PHP Code',
      status: 'publish',
      password: '',
      gist_id: '',
      sync: 'off',
      instances: [
        {
          key: '1',
          filename: 'file.php',
          code: 'echo "Hello";    \necho "world!";',
          cursor: [17, 17],
          language: 'php',
          history: {
            undo: [
              {
                code: 'echo "Hello";\necho "world!";',
                cursor: [13, 13],
              },
            ],
            redo: [],
          },
        },
      ],
    };

    expect(editorReducer(before, action)).toEqual(after);
  });

  it('should space indent by width when cursor is on multiple lines', () => {
    const action = editorIndentWithKey(
      {
        code: 'echo "Hello";\necho "world!";',
        cursor: [0, 28],
        inverse: false,
      },
      '1',
    );
    const before: EditorState = { ...initial, tabs: 'off' };
    const after = {
      errors: [],
      theme: 'default',
      tabs: 'off',
      width: '4',
      invisibles: 'off',
      description: 'PHP Code',
      status: 'publish',
      password: '',
      gist_id: '',
      sync: 'off',
      instances: [
        {
          key: '1',
          filename: 'file.php',
          code: '    echo "Hello";\n    echo "world!";',
          cursor: [4, 36],
          language: 'php',
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
        },
      ],
    };

    expect(editorReducer(before, action)).toEqual(after);
  });

  it('should delete tab next to cursor if inverse', () => {
    const action = editorIndentWithKey(
      {
        code: 'echo "Hello";\t\necho "world!";',
        cursor: [14, 14],
        inverse: true,
      },
      '1',
    );
    const before: EditorState = {
      ...initial,
      instances: [
        {
          ...initial.instances[0],
          code: 'echo "Hello";\t\necho "world!";',
          cursor: [14, 14],
        },
      ],
    };
    const after = {
      errors: [],
      theme: 'default',
      tabs: 'on',
      width: '4',
      invisibles: 'off',
      description: 'PHP Code',
      status: 'publish',
      password: '',
      gist_id: '',
      sync: 'off',
      instances: [
        {
          key: '1',
          filename: 'file.php',
          code: 'echo "Hello";\necho "world!";',
          cursor: [13, 13],
          language: 'php',
          history: {
            undo: [
              {
                code: 'echo "Hello";\t\necho "world!";',
                cursor: [14, 14],
              },
            ],
            redo: [],
          },
        },
      ],
    };

    expect(editorReducer(before, action)).toEqual(after);
  });

  it('should delete tab from line the cursor is on if inverse', () => {
    const action = editorIndentWithKey(
      {
        code: '\techo "Hello";\necho "world!";',
        cursor: [14, 14],
        inverse: true,
      },
      '1',
    );
    const before: EditorState = {
      ...initial,
      instances: [
        {
          ...initial.instances[0],
          code: '\techo "Hello";\necho "world!";',
          cursor: [14, 14],
        },
      ],
    };
    const after = {
      errors: [],
      theme: 'default',
      tabs: 'on',
      width: '4',
      invisibles: 'off',
      description: 'PHP Code',
      status: 'publish',
      password: '',
      gist_id: '',
      sync: 'off',
      instances: [
        {
          key: '1',
          filename: 'file.php',
          code: 'echo "Hello";\necho "world!";',
          cursor: [13, 13],
          language: 'php',
          history: {
            undo: [
              {
                code: '\techo "Hello";\necho "world!";',
                cursor: [14, 14],
              },
            ],
            redo: [],
          },
        },
      ],
    };

    expect(editorReducer(before, action)).toEqual(after);
  });

  it('should delete spaces before the cursor if inverse', () => {
    const action = editorIndentWithKey(
      {
        code: 'echo "Hello";    \necho "world!";',
        cursor: [17, 17],
        inverse: true,
      },
      '1',
    );
    const before: EditorState = {
      ...initial,
      tabs: 'off',
      instances: [
        {
          ...initial.instances[0],
          code: 'echo "Hello";    \necho "world!";',
          cursor: [17, 17],
        },
      ],
    };
    const after = {
      errors: [],
      theme: 'default',
      tabs: 'off',
      width: '4',
      invisibles: 'off',
      description: 'PHP Code',
      status: 'publish',
      password: '',
      gist_id: '',
      sync: 'off',
      instances: [
        {
          key: '1',
          filename: 'file.php',
          code: 'echo "Hello";\necho "world!";',
          cursor: [13, 13],
          language: 'php',
          history: {
            undo: [
              {
                code: 'echo "Hello";    \necho "world!";',
                cursor: [17, 17],
              },
            ],
            redo: [],
          },
        },
      ],
    };

    expect(editorReducer(before, action)).toEqual(after);
  });

  it('should delete spaces at start of line cursor is on if inverse', () => {
    const action = editorIndentWithKey(
      {
        code: '    echo "Hello";\necho "world!";',
        cursor: [17, 17],
        inverse: true,
      },
      '1',
    );
    const before: EditorState = {
      ...initial,
      tabs: 'off',
      instances: [
        {
          ...initial.instances[0],
          code: '    echo "Hello";\necho "world!";',
          cursor: [17, 17],
        },
      ],
    };
    const after = {
      errors: [],
      theme: 'default',
      tabs: 'off',
      width: '4',
      invisibles: 'off',
      description: 'PHP Code',
      status: 'publish',
      password: '',
      gist_id: '',
      sync: 'off',
      instances: [
        {
          key: '1',
          filename: 'file.php',
          code: 'echo "Hello";\necho "world!";',
          cursor: [13, 13],
          language: 'php',
          history: {
            undo: [
              {
                code: '    echo "Hello";\necho "world!";',
                cursor: [17, 17],
              },
            ],
            redo: [],
          },
        },
      ],
    };

    expect(editorReducer(before, action)).toEqual(after);
  });

  it('should add newline and indentation', () => {
    const action = editorMakeNewlineWithKey(
      {
        code: '    echo "Hello";\necho "world!";',
        cursor: [17, 17],
      },
      '1',
    );
    const before: EditorState = {
      ...initial,
      tabs: 'off',
      instances: [
        {
          ...initial.instances[0],
          code: '    echo "Hello";\necho "world!";',
          cursor: [17, 17],
        },
      ],
    };
    const after = {
      errors: [],
      theme: 'default',
      tabs: 'off',
      width: '4',
      invisibles: 'off',
      description: 'PHP Code',
      status: 'publish',
      password: '',
      gist_id: '',
      sync: 'off',
      instances: [
        {
          key: '1',
          filename: 'file.php',
          code: '    echo "Hello";\n    \necho "world!";',
          cursor: [22, 22],
          language: 'php',
          history: {
            undo: [
              {
                code: '    echo "Hello";\necho "world!";',
                cursor: [17, 17],
              },
            ],
            redo: [],
          },
        },
      ],
    };

    expect(editorReducer(before, action)).toEqual(after);
  });

  it('should remove indentation when cursor at beginning of line', () => {
    const action = editorIndentWithKey(
      {
        code: '    echo "Hello";\necho "world!";',
        cursor: [0, 0],
        inverse: true,
      },
      '1',
    );
    const before: EditorState = {
      ...initial,
      tabs: 'off',
      instances: [
        {
          ...initial.instances[0],
          code: '    echo "Hello";\necho "world!";',
          cursor: [0, 0],
        },
      ],
    };
    const after = {
      errors: [],
      theme: 'default',
      tabs: 'off',
      width: '4',
      invisibles: 'off',
      description: 'PHP Code',
      status: 'publish',
      password: '',
      gist_id: '',
      sync: 'off',
      instances: [
        {
          key: '1',
          filename: 'file.php',
          code: 'echo "Hello";\necho "world!";',
          cursor: [0, 0],
          language: 'php',
          history: {
            undo: [
              {
                code: '    echo "Hello";\necho "world!";',
                cursor: [0, 0],
              },
            ],
            redo: [],
          },
        },
      ],
    };

    expect(editorReducer(before, action)).toEqual(after);
  });
});
