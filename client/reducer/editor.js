import { EDITOR_OPTIONS_CLICK, EDITOR_INVISIBLES_TOGGLE, EDITOR_THEME_CHANGE,
    EDITOR_TABS_TOGGLE, EDITOR_WIDTH_CHANGE, EDITOR_VALUE_CHANGE,
    EDITOR_CURSOR_MOVE, EDITOR_INDENT, EDITOR_MAKE_NEWLINE,
    EDITOR_DESCRIPTION_CHANGE, EDITOR_STATUS_CHANGE, EDITOR_SYNC_TOGGLE } from '../action';

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
    instances: [{
        filename: '',
        code: '',
        language: 'plaintext',
        cursor: false,
        history: {
            undo: [],
            redo: []
        }
    }]
};

/**
 * Returns a new editor state for a given action.
 *
 * @param {Object} state - Current editor state.
 * @param {string} type - Action type.
 * @param {Object} payload - Action payload.
 * @returns {Object} New editor state.
 */
export default function editorReducer(state = defaults, { type, payload } = {}) {
    switch (type) {
        case EDITOR_OPTIONS_CLICK:
            return { ...state, optionsOpen: !state.optionsOpen };
        case EDITOR_THEME_CHANGE:
            return { ...state, theme: payload.value };
        case EDITOR_TABS_TOGGLE:
            return { ...state, tabs: payload.value };
        case EDITOR_WIDTH_CHANGE:
            return { ...state, width: payload.value };
        case EDITOR_INVISIBLES_TOGGLE:
            return { ...state, invisibles: payload.value };
        case EDITOR_CURSOR_MOVE:
            return mapInstanceWithKey(state, payload.key, instance => ({
                ...instance,
                cursor: payload.cursor
            }));
        case EDITOR_DESCRIPTION_CHANGE:
            return { ...state, description: payload.value };
        case EDITOR_STATUS_CHANGE:
            return { ...state, status: payload.value };
        case EDITOR_SYNC_TOGGLE:
            return { ...state, sync: payload.value };
        case EDITOR_INDENT:
            return mapInstanceWithKey(state, payload.key, instance => ({
                ...instance,
                ...indent(payload, state),
                history: {
                    ...instance.history,
                    undo: instance.history.undo.concat({
                        code: instance.code,
                        cursor: instance.cursor
                    })
                }
            }));
        case EDITOR_MAKE_NEWLINE:
            return mapInstanceWithKey(state, payload.key, instance => ({
                ...instance,
                ...makeNewline(payload),
                history: {
                    ...instance.history,
                    undo: instance.history.undo.concat({
                        code: instance.code,
                        cursor: instance.cursor
                    })
                }
            }));
        case EDITOR_VALUE_CHANGE:
            return mapInstanceWithKey(state, payload.key, instance => ({
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
            }));
        default:
            return state;
    }
};

/**
 * Modify a single instance by key.
 *
 * @param {Object} state - Current state.
 * @param {string} key - Instance key to modify.
 * @param {Function} fn - Function to call
 * @returns {Object} New State.
 */
function mapInstanceWithKey(state, key, fn) {
    return { ...state, instances: state.instances.map(instance =>
        instance.key !== key ? instance : fn(instance)
    ) };
}

/**
 * Extract code sections based on selection start & end.
 *
 * @param {string} code - Current code in editor.
 * @param {number} ss - Selection start.
 * @param {number} se - Selection end.s
 * @returns {{before: string, selection: string, after: string}} Code section.
 */
function extractSections(code, ss, se) {
    return {
        before: code.slice(0, ss),
        selection: code.slice(ss, se),
        after: code.slice(se)
    };
}

/**
 * Update the code and cursor position for indentation.
 *
 * @param {string} code - Current code in the editor.
 * @param {number} ss - Selection start.
 * @param {number} se - Selection end.
 * @param {boolean} inverse - Whether the indentation should be inverse.
 * @param {string} tabs - Whether tabs are "on" or "off".
 * @param {string} width - Width of tabs.
 * @returns {{code: string, cursor: [number, number]}} New code and cursor position.
 */
function indent({ code, cursor: [ss,se], inverse }, { tabs, width }) {
    let { before, selection, after } = extractSections(code, ss, se);

    if (inverse) {
        if (tabs === 'on') {
            if ('\t' === before.charAt(before.length - 1)) {
                before = before.slice(0, -1);
                ss--;
            } else {
                let befores = before.split('\n');

                if ('\t' === befores[befores.length - 1].charAt(0)) {
                    befores[befores.length - 1] = befores[befores.length - 1].slice(1);
                    ss--;
                }

                before = befores.join('\n');
            }
        } else {
            if (' ' === before.charAt(before.length - 1)) {
                width = parseInt(width, 10);

                while (width && ' ' === before.charAt(before.length - 1)) {
                    before = before.slice(0, -1);
                    width--;
                    ss--;
                }
            } else {
                let befores = before.split('\n');

                while (width && ' ' === befores[befores.length - 1].charAt(0)) {
                    befores[befores.length - 1] = befores[befores.length - 1].slice(1);
                    width--;
                    ss--;
                }

                before = befores.join('\n');
            }
        }
    } else {
        const append = tabs === 'on' ? '\t' : new Array(parseInt(width, 10) + 1).join(' ');

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
 * @param {number} ss - Selection start.
 * @param {number} se - Selection end.
 * @returns {{code: string, cursor: [number, number]}} New code and cursor position.
 */
function makeNewline({ code, cursor: [ss,se] }) {
    let { before, after } = extractSections(code, ss, se);

    let lf = before.lastIndexOf('\n') + 1;
    let indent = (before.slice(lf).match(/^\s+/) || [''])[0];

    before += '\n' + indent;

    ss += indent.length + 1;
    se = ss;

    return {
        code: before + after,
        cursor: [ss, se]
    };
}
