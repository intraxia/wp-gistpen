import R from 'ramda';
import events from 'brookjs/events';
import { editorCursorMoveAction, editorIndentAction, editorMakeCommentAction,
    editorMakeNewlineAction, editorRedoAction, editorUndoAction,
    editorValueChangeAction, editorFilenameChangeAction,
    editorLanguageChangeAction } from '../../action';
import { selectSelectionStart, selectSelectionEnd } from '../../selector';
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
const mapKeydownToAction = function mapKeydownToAction(evt) {
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
};

export default events({
    onBlur: R.map(R.always(editorCursorMoveAction(false))),
    onClick: mapToTargetCursorAction,
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
        R.filter(({ keyCode }) =>
            keyCode < 9 || keyCode > 32 && keyCode < 41
        ),
        mapToTargetCursorAction
    ),
    onInput: R.map(({ target }) =>
        editorValueChangeAction({
            code: target.textContent,
            cursor: [selectSelectionStart(target), selectSelectionEnd(target)]
        })
    ),
    onLanguage: R.map(R.pipe(
        R.path(['target', 'value']),
        editorLanguageChangeAction
    ))
});
