import { EDITOR_OPTIONS_CLICK, EDITOR_INVISIBLES_TOGGLE, EDITOR_THEME_CHANGE,
    EDITOR_TABS_TOGGLE, EDITOR_WIDTH_CHANGE, EDITOR_VALUE_CHANGE } from '../action';

const defaults = {
    optionsOpen: false,
    theme: 'default',
    tabs: 'off',
    width: '4',
    invisibles: 'off',
    code: '',
    cursor: false,
    history: {
        undo: [],
        redo: []
    }
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
        case EDITOR_VALUE_CHANGE:
            return { ...state, instances: state.instances.map(instance =>
                instance.key !== payload.key ? instance : {
                    ...instance,
                    code: payload.code,
                    cursor: payload.cursor,
                    history: {
                        ...instance.history,
                        undo: instance.history.undo.concat({
                            code: instance.code,
                            cursor: instance.cursor,
                            add: payload.add,
                            del: payload.del
                        })
                    }
                }
            ) };
        default:
            return state;
    }
};
