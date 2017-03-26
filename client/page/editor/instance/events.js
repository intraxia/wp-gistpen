// @flow
import type { Action } from '../../../type';
import R from 'ramda';
import { events } from 'brookjs';
import { editorCursorMoveAction, editorIndentAction, editorMakeCommentAction,
    editorMakeNewlineAction, editorRedoAction, editorUndoAction,
    editorValueChangeAction, editorFilenameChangeAction, editorDeleteClickAction,
    editorLanguageChangeAction } from '../../../action';
import { selectSelectionStart, selectSelectionEnd } from '../../../selector';
import { isSpecialEvent } from './util';

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
function mapKeydownToAction(evt : Event) : Action {
    const { shiftKey: inverse } = evt;
    const { textContent: code } = evt.target;
    const cursor = [selectSelectionStart(evt.target), selectSelectionEnd(evt.target)];

    evt.preventDefault();

    switch (evt.keyCode) {
        case 9: // Tab
            return editorIndentAction({ code, cursor, inverse });
        case 13:
            return editorMakeNewlineAction({ code, cursor });
        case 90:
            return inverse ? editorRedoAction() : editorUndoAction();
        case 191:
            return editorMakeCommentAction({ code, cursor });
    }

    throw new Error('Keydown is missing matching action case', evt);
}

export default events({
    onBlur: R.map(R.always(editorCursorMoveAction(false))),
    onClick: mapToTargetCursorAction,
    onDeleteClick: R.map(editorDeleteClickAction),
    onFilename: R.map(R.pipe(
        R.path(['target', 'textContent']),
        editorFilenameChangeAction
    )),
    onFocus: mapToTargetCursorAction,
    onKeydown: R.pipe(
        R.filter(isSpecialEvent),
        R.map(mapKeydownToAction)
    ),
    onKeyup: R.pipe(
        R.filter(R.pipe(isSpecialEvent, R.not)),
        mapToTargetCursorAction
    ),
    onInput: R.map((evt : Event) =>
        editorValueChangeAction({
            code: evt.target.textContent,
            cursor: [selectSelectionStart(evt.target), selectSelectionEnd(evt.target)]
        })
    ),
    onLanguage: R.map(R.pipe(
        R.path(['target', 'value']),
        editorLanguageChangeAction
    ))
});
