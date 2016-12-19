/**
 * Returns whether any of the editor options have changed.
 *
 * @param {Object} prev - Previous props.
 * @param {Obhject} next - Next props.
 * @returns {boolean} Whether any editor options have changed.
 */
export const editorOptionsIsEqual = function editorOptionsIsEqual(prev, next) {
    return prev.editor.theme === next.editor.theme &&
        prev.editor.invisibles === next.editor.invisibles;
};

/**
 * Returns whether the line numbers have changed.
 *
 * @todo implement with line numbers.
 * @returns {boolean} Whether any the line numbers have changed.
 */
export const lineNumberIsEqual = function lineNumberIsEqual(/* prev, next */) {
    return true;
};

/**
 * Returns whether the event object is a special event, hadled
 * by the reducer logic.
 *
 * @param {Event} evt - Event object.
 * @returns {boolean} Whether this is a special event.
 */
export const isSpecialEvent = function isSpecialEvent(evt) {
    const { altKey, metaKey, ctrlKey } = evt;
    const cmdOrCtrl = metaKey || ctrlKey;

    switch (evt.keyCode) {
        case 9: // Tab
            if (!cmdOrCtrl) {
                return true;
            }
            break;
        case 13:
            return true;
        case 90:
            if (cmdOrCtrl) {
                return true;
            }
            break;
        case 191:
            if (cmdOrCtrl && !altKey) {
                return true;
            }
            break;
    }

    return false;
};
