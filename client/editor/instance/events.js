import R from 'ramda';
import events from 'brookjs/events';
import { editorCursorMoveAction, editorIndentAction, editorMakeCommentAction,
    editorMakeNewlineAction, editorRedoAction, editorUndoAction,
    editorValueChangeAction } from '../../action';
import { selectSelectionStart, selectSelectionEnd } from '../../selector';

/**
 * Maps an element's selection start/end to the Cursor Move Action.
 *
 * @type {Function}
 */
const elementToCursorMoveAction = R.converge(
    R.unapply(editorCursorMoveAction),
    [selectSelectionStart, selectSelectionEnd]
);

/**
 * Maps an event to its element's cursor action.
 *
 * @type {Function}
 */
const mapToTargetCursorAction = R.map(R.pipe(
    R.prop('target'),
    elementToCursorMoveAction
));

/**
 * Maps the Keydown event to an Action, or false if no relevant action.
 *
 * @param {Event} evt - DOM Event object.
 * @returns {Action|false} Action to emit, or false if no action.
 */
const mapKeydownToAction = function mapKeydownToAction(evt) {
    const { altKey, shiftKey: inverse, metaKey, ctrlKey } = evt;
    const cmdOrCtrl = metaKey || ctrlKey;
    const { textContent: code } = evt.target;
    const cursor = [selectSelectionStart(evt.target), selectSelectionEnd(evt.target)];

    switch (evt.keyCode) {
        case 9: // Tab
            if (!cmdOrCtrl) {
                return editorIndentAction({ code, cursor, inverse });
            }
            break;
        case 13:
            return editorMakeNewlineAction({ code, cursor });
        case 90:
            if (cmdOrCtrl) {
                return inverse ? editorRedoAction() : editorUndoAction();
            }
            break;
        case 191:
            if (cmdOrCtrl && !altKey) {
                return editorMakeCommentAction({ code, cursor });
            }
            break;
    }

    return false;
};

export default events({
    onBlur: R.map(R.always(editorCursorMoveAction(false))),
    onClick: mapToTargetCursorAction,
    onFocus: mapToTargetCursorAction,
    onKeydown: R.pipe(
        R.map(mapKeydownToAction),
        R.filter(R.identity)
    ),
    onInput: R.map(({ target }) =>
        editorValueChangeAction({
            code: target.textContent,
            cursor: [selectSelectionStart(target), selectSelectionEnd(target)]
        })
    )
});
