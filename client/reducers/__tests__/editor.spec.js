// @flow
/* eslint-env mocha */
import type { EditorIndentAction, EditorMakeNewLineAction, EditorState, HasMetaKey } from '../../types';
import { expect } from 'chai';
import { editorIndentAction, editorMakeNewlineAction } from '../../actions';
import editorReducer from '../editor';

describe('Editor Reducer', () => {
    const initial : EditorState = {
        theme: 'default',
        tabs: 'on',
        width: '4',
        invisibles: 'off',
        description: 'PHP Code',
        status: 'publish',
        password: '',
        gist_id: '',
        sync: 'off',
        instances: [{
            key: '1',
            filename: 'file.php',
            code: 'echo "Hello";\necho "world!";',
            cursor: [13, 13],
            language: 'php',
            history: {
                undo: [],
                redo: []
            }
        }]
    };

    it('should tab indent line the cursor is on', () => {
        const action : EditorIndentAction & HasMetaKey = {
            ...editorIndentAction({
                code: 'echo "Hello";\necho "world!";',
                cursor: [13, 13],
                inverse: false
            }),
            meta: {
                key: '1'
            }
        };
        const before : EditorState = initial;
        const after : EditorState = {
            theme: 'default',
            tabs: 'on',
            width: '4',
            invisibles: 'off',
            description: 'PHP Code',
            status: 'publish',
            password: '',
            gist_id: '',
            sync: 'off',
            instances: [{
                key: '1',
                filename: 'file.php',
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

        expect(editorReducer(before, action)).to.eql(after);
    });

    it('should space indent by width line the cursor is on', () => {
        const action : EditorIndentAction & HasMetaKey = Object.assign({}, editorIndentAction({
            code: 'echo "Hello";\necho "world!";',
            cursor: [13, 13],
            inverse: false
        }), { meta: { key: '1' } });
        const before : EditorState = { ...initial, tabs: 'off' };
        const after : EditorState = {
            theme: 'default',
            tabs: 'off',
            width: '4',
            invisibles: 'off',
            description: 'PHP Code',
            status: 'publish',
            password: '',
            gist_id: '',
            sync: 'off',
            instances: [{
                key: '1',
                filename: 'file.php',
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

        expect(editorReducer(before, action)).to.eql(after);
    });

    it('should space indent by width when cursor is on multiple lines', () => {
        const action : EditorIndentAction & HasMetaKey = {
            ...editorIndentAction({
                code: 'echo "Hello";\necho "world!";',
                cursor: [0, 28],
                inverse: false
            }),
            meta: { key: '1' }
        };
        const before : EditorState = { ...initial, tabs: 'off' };
        const after : EditorState = {
            theme: 'default',
            tabs: 'off',
            width: '4',
            invisibles: 'off',
            description: 'PHP Code',
            status: 'publish',
            password: '',
            gist_id: '',
            sync: 'off',
            instances: [{
                key: '1',
                filename: 'file.php',
                code: '    echo "Hello";\n    echo "world!";',
                cursor: [4, 36],
                language: 'php',
                history: {
                    undo: [{
                        code: 'echo "Hello";\necho "world!";',
                        // This matches the previous state, not the dispatched value.
                        cursor: [13, 13],
                    }],
                    redo: []
                }
            }]
        };

        expect(editorReducer(before, action)).to.eql(after);
    });

    it('should delete tab next to cursor if inverse', () => {
        const action : EditorIndentAction & HasMetaKey = {
            ...editorIndentAction({
                code: 'echo "Hello";\t\necho "world!";',
                cursor: [14, 14],
                inverse: true
            }),
            meta: {
                key: '1'
            }
        };
        const before : EditorState = {
            ...initial,
            instances: [{
                ...initial.instances[0],
                code: 'echo "Hello";\t\necho "world!";',
                cursor: [14, 14],
            }]
        };
        const after : EditorState = {
            theme: 'default',
            tabs: 'on',
            width: '4',
            invisibles: 'off',
            description: 'PHP Code',
            status: 'publish',
            password: '',
            gist_id: '',
            sync: 'off',
            instances: [{
                key: '1',
                filename: 'file.php',
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

        expect(editorReducer(before, action)).to.eql(after);
    });

    it('should delete tab from line the cursor is on if inverse', () => {
        const action : EditorIndentAction & HasMetaKey = {
            ...editorIndentAction({
                code: '\techo "Hello";\necho "world!";',
                cursor: [14, 14],
                inverse: true
            }),
            meta: {
                key: '1'
            }
        };
        const before : EditorState = {
            ...initial,
            instances: [{
                ...initial.instances[0],
                code: '\techo "Hello";\necho "world!";',
                cursor: [14, 14],
            }]
        };
        const after : EditorState = {
            theme: 'default',
            tabs: 'on',
            width: '4',
            invisibles: 'off',
            description: 'PHP Code',
            status: 'publish',
            password: '',
            gist_id: '',
            sync: 'off',
            instances: [{
                key: '1',
                filename: 'file.php',
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

        expect(editorReducer(before, action)).to.eql(after);
    });

    it('should delete spaces before the cursor if inverse', () => {
        const action : EditorIndentAction & HasMetaKey = {
            ...editorIndentAction({
                code: 'echo "Hello";    \necho "world!";',
                cursor: [17, 17],
                inverse: true
            }),
            meta: {
                key: '1'
            }
        };
        const before : EditorState = {
            ...initial,
            tabs: 'off',
            instances: [{
                ...initial.instances[0],
                code: 'echo "Hello";    \necho "world!";',
                cursor: [17, 17],
            }]
        };
        const after : EditorState = {
            theme: 'default',
            tabs: 'off',
            width: '4',
            invisibles: 'off',
            description: 'PHP Code',
            status: 'publish',
            password: '',
            gist_id: '',
            sync: 'off',
            instances: [{
                key: '1',
                filename: 'file.php',
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

        expect(editorReducer(before, action)).to.eql(after);
    });

    it('should delete spaces at start of line cursor is on if inverse', () => {
        const action : EditorIndentAction & HasMetaKey = {
            ...editorIndentAction({
                code: '    echo "Hello";\necho "world!";',
                cursor: [17, 17],
                inverse: true
            }),
            meta: {
                key: '1'
            }
        };
        const before : EditorState = {
            ...initial,
            tabs: 'off',
            instances: [{
                ...initial.instances[0],
                code: '    echo "Hello";\necho "world!";',
                cursor: [17, 17],
            }]
        };
        const after : EditorState = {
            theme: 'default',
            tabs: 'off',
            width: '4',
            invisibles: 'off',
            description: 'PHP Code',
            status: 'publish',
            password: '',
            gist_id: '',
            sync: 'off',
            instances: [{
                key: '1',
                filename: 'file.php',
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

        expect(editorReducer(before, action)).to.eql(after);
    });

    it('should add newline and indentation', () => {
        const action : EditorMakeNewLineAction & HasMetaKey = {
            ...editorMakeNewlineAction({
                code: '    echo "Hello";\necho "world!";',
                cursor: [17, 17]
            }),
            meta: {
                key: '1'
            }
        };
        const before : EditorState = {
            ...initial,
            tabs: 'off',
            instances: [{
                ...initial.instances[0],
                code: '    echo "Hello";\necho "world!";',
                cursor: [17, 17],
            }]
        };
        const after : EditorState = {
            theme: 'default',
            tabs: 'off',
            width: '4',
            invisibles: 'off',
            description: 'PHP Code',
            status: 'publish',
            password: '',
            gist_id: '',
            sync: 'off',
            instances: [{
                key: '1',
                filename: 'file.php',
                code: '    echo "Hello";\n    \necho "world!";',
                cursor: [22, 22],
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

        expect(editorReducer(before, action)).to.eql(after);
    });

    it('should remove indentation when cursor at beginning of line', () => {
        const action : EditorIndentAction & HasMetaKey = {
            ...editorIndentAction({
                code: '    echo "Hello";\necho "world!";',
                cursor: [0, 0],
                inverse: true
            }),
            meta: {
                key: '1'
            }
        };
        const before : EditorState = {
            ...initial,
            tabs: 'off',
            instances: [{
                ...initial.instances[0],
                code: '    echo "Hello";\necho "world!";',
                cursor: [0, 0],
            }]
        };
        const after : EditorState = {
            theme: 'default',
            tabs: 'off',
            width: '4',
            invisibles: 'off',
            description: 'PHP Code',
            status: 'publish',
            password: '',
            gist_id: '',
            sync: 'off',
            instances: [{
                key: '1',
                filename: 'file.php',
                code: 'echo "Hello";\necho "world!";',
                cursor: [0, 0],
                language: 'php',
                history: {
                    undo: [{
                        code: '    echo "Hello";\necho "world!";',
                        cursor: [0, 0]
                    }],
                    redo: []
                }
            }]
        };

        expect(editorReducer(before, action)).to.eql(after);
    });
});
