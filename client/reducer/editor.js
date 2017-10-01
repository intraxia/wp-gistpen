// @flow
import type { HasMetaKey, EditorInstance, EditorValue, EditorIndentValue, EditorState,
    EditorThemeChangeAction, EditorTabsToggleAction, EditorWidthChangeAction,
    EditorInvisiblesToggleAction, EditorCursorMoveAction, EditorDescriptionChangeAction,
    EditorStatusChangeAction, EditorSyncChangeAction, EditorDeleteClickAction,
    EditorFilenameChangeAction, EditorLanguageChangeAction, EditorIndentAction,
    EditorMakeNewLineAction, EditorValueChangeAction, RepoSaveSucceededAction, Blob } from '../type';
import R from 'ramda';
import { combineActionReducers } from 'brookjs';
import { EDITOR_OPTIONS_CLICK, EDITOR_INVISIBLES_TOGGLE, EDITOR_THEME_CHANGE,
    EDITOR_TABS_TOGGLE, EDITOR_WIDTH_CHANGE, EDITOR_VALUE_CHANGE, EDITOR_DELETE_CLICK,
    EDITOR_CURSOR_MOVE, EDITOR_INDENT, EDITOR_MAKE_NEWLINE, REPO_SAVE_SUCCEEDED,
    EDITOR_DESCRIPTION_CHANGE, EDITOR_STATUS_CHANGE, EDITOR_SYNC_TOGGLE,
    EDITOR_FILENAME_CHANGE, EDITOR_LANGUAGE_CHANGE, EDITOR_ADD_CLICK } from '../action';

const defaultInstance = {
    filename: '',
    code: '\n',
    language: 'plaintext',
    cursor: false,
    history: {
        undo: [],
        redo: []
    }
};

const defaults = {
    optionsOpen: false,
    theme: 'default',
    tabs: 'off',
    width: '4',
    invisibles: 'off',
    description: '',
    status: 'draft',
    password: '',
    gist_id: '',
    sync: 'off',
    instances: [{ ...defaultInstance, key: createUniqueKey([]) }]
};

export default combineActionReducers([
    [EDITOR_OPTIONS_CLICK, (state : EditorState) => ({
        ...state,
        optionsOpen: !state.optionsOpen
    })],
    [EDITOR_THEME_CHANGE, (state : EditorState, { payload } : EditorThemeChangeAction) => ({
        ...state,
        theme: payload.value
    })],
    [EDITOR_TABS_TOGGLE, (state : EditorState, { payload } : EditorTabsToggleAction) => ({
        ...state,
        tabs: payload.value
    })],
    [EDITOR_WIDTH_CHANGE, (state : EditorState, { payload } : EditorWidthChangeAction) => ({
        ...state,
        width: payload.value
    })],
    [EDITOR_INVISIBLES_TOGGLE, (state : EditorState, { payload } : EditorInvisiblesToggleAction) => ({
        ...state,
        invisibles: payload.value
    })],
    [EDITOR_DESCRIPTION_CHANGE, (state : EditorState, { payload } : EditorDescriptionChangeAction) => ({
        ...state,
        description: payload.value
    })],
    [EDITOR_STATUS_CHANGE, (state : EditorState, { payload } : EditorStatusChangeAction) => ({
        ...state,
        status: payload.value
    })],
    [EDITOR_SYNC_TOGGLE, (state : EditorState, { payload } : EditorSyncChangeAction) => ({
        ...state,
        sync: payload.value
    })],
    [EDITOR_ADD_CLICK, (state : EditorState) => ({
        ...state,
        instances: [...state.instances, {
            ...defaultInstance,
            key: createUniqueKey(state.instances)
        }]
    })],
    [EDITOR_DELETE_CLICK, (state : EditorState, { meta } : EditorDeleteClickAction & HasMetaKey) => ({
        ...state,
        instances: rejectWithKey(meta.key, state.instances)
    })],
    [EDITOR_CURSOR_MOVE, (state : EditorState, { payload, meta } : EditorCursorMoveAction & HasMetaKey) => mapInstanceWithKey(
        state,
        meta.key,
        (instance : EditorInstance)=> ({
            ...instance,
            cursor: payload.cursor
        })
    )],
    [EDITOR_FILENAME_CHANGE, (state : EditorState, { payload, meta } : EditorFilenameChangeAction & HasMetaKey) => mapInstanceWithKey(state, meta.key, (instance : EditorInstance) => ({
        ...instance,
        filename: payload.value
    }))],
    [EDITOR_LANGUAGE_CHANGE, (state : EditorState, { payload, meta } : EditorLanguageChangeAction & HasMetaKey) => mapInstanceWithKey(state, meta.key, (instance : EditorInstance) => ({
        ...instance,
        language: payload.value
    }))],
    [EDITOR_INDENT, (state : EditorState, { payload, meta } : EditorIndentAction & HasMetaKey) => mapInstanceWithKey(state, meta.key, (instance : EditorInstance) => ({
        ...instance,
        ...indent(payload, { tabs: state.tabs, width: state.width }),
        history: {
            ...instance.history,
            undo: instance.history.undo.concat({
                code: instance.code,
                cursor: instance.cursor
            })
        }
    }))],
    [EDITOR_MAKE_NEWLINE, (state : EditorState, { payload, meta } : EditorMakeNewLineAction & HasMetaKey) => mapInstanceWithKey(state, meta.key, (instance : EditorInstance) => ({
        ...instance,
        ...makeNewline(payload),
        history: {
            ...instance.history,
            undo: instance.history.undo.concat({
                code: instance.code,
                cursor: instance.cursor
            })
        }
    }))],
    [EDITOR_VALUE_CHANGE, (state : EditorState, { payload, meta } : EditorValueChangeAction & HasMetaKey) => mapInstanceWithKey(state, meta.key, (instance : EditorInstance) => ({
        ...instance,
        code: payload.code,
        cursor: payload.cursor,
        history: {
            ...instance.history,
            undo: instance.history.undo.concat({
                code: instance.code,
                cursor: instance.cursor
            })
        }
    }))],
    [REPO_SAVE_SUCCEEDED, (state : EditorState, { payload } : RepoSaveSucceededAction) : EditorState => {
        const { response: repo } = payload;
        return {
            ...state,
            description: repo.description,
            status: repo.status,
            password: repo.password,
            gist_id: repo.gist_id,
            sync: repo.sync,
            instances: repo.blobs.map((blob : Blob) => ({
                ...defaultInstance,
                key: blob.ID != null ? String(blob.ID)  : '',
                filename: blob.filename,
                code: blob.code,
                language: typeof blob.language === 'string' ? blob.language : blob.language.slug
            }))
        };
    }]
], defaults);

/**
 * Returns an updated array with the instance matching the provided key removed.
 *
 * @param {string} key - Key to remove.
 * @param {Instance[]} instances - Current instances
 * @returns {Instance[]} Update instances.
 */
function rejectWithKey(key : string, instances : Array<EditorInstance>) : Array<EditorInstance> {
    return R.reject((instance : EditorInstance)=> key === instance.key, instances);
}

/**
 * Modify a single instance by key.
 *
 * @param {Object} state - Current state.
 * @param {string} key - Instance key to modify.
 * @param {Function} fn - Function to call
 * @returns {Object} New State.
 */
function mapInstanceWithKey(state : EditorState, key : string, fn : (i : EditorInstance) => EditorInstance) : EditorState {
    return { ...state, instances: state.instances.map((instance : EditorInstance) =>
        instance.key !== key ? instance : fn(instance)
    ) };
}

type Section = {
    before : string;
    selection : string;
    after : string;
};

/**
 * Extract code sections based on selection start & end.
 *
 * @param {string} code - Current code in editor.
 * @param {number} ss - Selection start.
 * @param {number} se - Selection end.s
 * @returns {Section} Code section.
 */
function extractSections(code : string, ss : number, se : number) : Section {
    return {
        before: code.slice(0, ss),
        selection: code.slice(ss, se),
        after: code.slice(se)
    };
}

type Indentation = {
    tabs : string;
    width : string;
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
function indent({ code, cursor, inverse } : EditorIndentValue, { tabs, width } : Indentation) : EditorValue {
    if (!cursor) {
        return { code, cursor, inverse };
    }
    let [ss,se] = cursor;
    let w = parseInt(width, 10);
    let { before, selection, after } = extractSections(code, ss, se);

    if (inverse) {
        if (tabs === 'on') {
            if ('\t' === before.charAt(before.length - 1)) {
                before = before.slice(0, -1);
                ss--;
            } else {
                const befores = before.split('\n');

                if ('\t' === befores[befores.length - 1].charAt(0)) {
                    befores[befores.length - 1] = befores[befores.length - 1].slice(1);
                    ss--;
                }

                before = befores.join('\n');
            }
        } else {
            if (' ' === before.charAt(before.length - 1)) {
                while (w && ' ' === before.charAt(before.length - 1)) {
                    before = before.slice(0, -1);
                    w--;
                    ss--;
                }
            } else {
                const befores = before.split('\n');

                while (w && ' ' === befores[befores.length - 1].charAt(0)) {
                    befores[befores.length - 1] = befores[befores.length - 1].slice(1);
                    w--;
                    ss--;
                }

                before = befores.join('\n');
            }
        }
    } else {
        const append = tabs === 'on' ? '\t' : new Array(w + 1).join(' ');

        before += append;

        ss += append.length;
        se += append.length;

        return {
            code: before + selection + after,
            cursor: [ss, se]
        };
    }

    se = ss + selection.length;

    return {
        code: before + selection + after,
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
function makeNewline({ code, cursor } : EditorValue) : EditorValue {
    if (!cursor) {
        return { code, cursor };
    }

    let [ss,se] = cursor;
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
function createUniqueKey(instances : Array<EditorInstance>) : string {
    const keys = instances.map(R.prop('key'));

    let id = 0;

    while (true) {
        const key = 'new' + id;

        if (keys.indexOf(key) === -1) {
            return key;
        }

        id++;
    }

    // Fix Flow's implicitly-returned undefined.
    return 'new-1';
}
