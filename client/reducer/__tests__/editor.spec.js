import test from 'ava';
import { editorIndentAction } from '../../action';
import editorReducer from '../editor';

const initial = {
    optionsOpen: false,
    theme: 'default',
    tabs: 'on',
    width: '4',
    invisibles: 'off',
    instances: [{
        code: 'echo "Hello";\necho "world!";',
        cursor: [13, 13],
        language: 'php',
        history: {
            undo: [],
            redo: []
        }
    }]
};

test('should tab indent line the cursor is on', t => {
    const action = editorIndentAction({
        code: 'echo "Hello";\necho "world!";',
        cursor: [13, 13],
        inverse: false
    });
    const before = initial;
    const after = {
        optionsOpen: false,
        theme: 'default',
        tabs: 'on',
        width: '4',
        invisibles: 'off',
        instances: [{
            code: 'echo "Hello";\t\necho "world!";',
            cursor: [14, 14],
            language: 'php',
            history: {
                undo: [{
                    code: 'echo "Hello";\necho "world!";',
                    cursor: [13, 13]
                }],
                redo: []
            }
        }]
    };

    t.deepEqual(editorReducer(before, action), after);
});

test('should space indent by width line the cursor is on', t => {
    const action = editorIndentAction({
        code: 'echo "Hello";\necho "world!";',
        cursor: [13, 13],
        inverse: false
    });
    const before = { ...initial, tabs: 'off' };
    const after = {
        optionsOpen: false,
        theme: 'default',
        tabs: 'off',
        width: '4',
        invisibles: 'off',
        instances: [{
            code: 'echo "Hello";    \necho "world!";',
            cursor: [17, 17],
            language: 'php',
            history: {
                undo: [{
                    code: 'echo "Hello";\necho "world!";',
                    cursor: [13, 13]
                }],
                redo: []
            }
        }]
    };

    t.deepEqual(editorReducer(before, action), after);
});

test('should delete tab next to cursor if inverse', t => {
    const action = editorIndentAction({
        code: 'echo "Hello";\t\necho "world!";',
        cursor: [14, 14],
        inverse: true
    });
    const before = {
        ...initial,
        instances: [{
            ...initial.instances[0],
            code: 'echo "Hello";\t\necho "world!";',
            cursor: [14, 14],
        }]
    };
    const after = {
        optionsOpen: false,
        theme: 'default',
        tabs: 'on',
        width: '4',
        invisibles: 'off',
        instances: [{
            code: 'echo "Hello";\necho "world!";',
            cursor: [13, 13],
            language: 'php',
            history: {
                undo: [{
                    code: 'echo "Hello";\t\necho "world!";',
                    cursor: [14, 14]
                }],
                redo: []
            }
        }]
    };

    t.deepEqual(editorReducer(before, action), after);
});

test('should delete tab from line the cursor is on if inverse', t => {
    const action = editorIndentAction({
        code: '\techo "Hello";\necho "world!";',
        cursor: [14, 14],
        inverse: true
    });
    const before = {
        ...initial,
        instances: [{
            ...initial.instances[0],
            code: '\techo "Hello";\necho "world!";',
            cursor: [14, 14],
        }]
    };
    const after = {
        optionsOpen: false,
        theme: 'default',
        tabs: 'on',
        width: '4',
        invisibles: 'off',
        instances: [{
            code: 'echo "Hello";\necho "world!";',
            cursor: [13, 13],
            language: 'php',
            history: {
                undo: [{
                    code: '\techo "Hello";\necho "world!";',
                    cursor: [14, 14]
                }],
                redo: []
            }
        }]
    };

    t.deepEqual(editorReducer(before, action), after);
});

test('should delete spaces before the cursor if inverse', t => {
    const action = editorIndentAction({
        code: 'echo "Hello";    \necho "world!";',
        cursor: [17, 17],
        inverse: true
    });
    const before = {
        ...initial,
        tabs: 'off',
        instances: [{
            ...initial.instances[0],
            code: 'echo "Hello";    \necho "world!";',
            cursor: [17, 17],
        }]
    };
    const after = {
        optionsOpen: false,
        theme: 'default',
        tabs: 'off',
        width: '4',
        invisibles: 'off',
        instances: [{
            code: 'echo "Hello";\necho "world!";',
            cursor: [13, 13],
            language: 'php',
            history: {
                undo: [{
                    code: 'echo "Hello";    \necho "world!";',
                    cursor: [17, 17]
                }],
                redo: []
            }
        }]
    };

    t.deepEqual(editorReducer(before, action), after);
});

test('should delete spaces at start of line cursor is on if inverse', t => {
    const action = editorIndentAction({
        code: '    echo "Hello";\necho "world!";',
        cursor: [17, 17],
        inverse: true
    });
    const before = {
        ...initial,
        tabs: 'off',
        instances: [{
            ...initial.instances[0],
            code: '    echo "Hello";\necho "world!";',
            cursor: [17, 17],
        }]
    };
    const after = {
        optionsOpen: false,
        theme: 'default',
        tabs: 'off',
        width: '4',
        invisibles: 'off',
        instances: [{
            code: 'echo "Hello";\necho "world!";',
            cursor: [13, 13],
            language: 'php',
            history: {
                undo: [{
                    code: '    echo "Hello";\necho "world!";',
                    cursor: [17, 17]
                }],
                redo: []
            }
        }]
    };

    t.deepEqual(editorReducer(before, action), after);
});
