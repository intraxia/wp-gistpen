// @flow
// @jsx h
import type { Observable } from 'kefir';
import type { Action, ObservableProps } from '../../../types';
import R from 'ramda';
import Kefir from 'kefir';
import { raf$ } from 'brookjs';
import { toJunction, h, view, withRef$ } from 'brookjs-silt';
import { editorCursorMoveAction, editorIndentAction, editorMakeCommentAction,
    editorMakeNewlineAction, editorRedoAction, editorUndoAction,
    editorValueChangeAction } from '../../../actions';
import { selectSelectionStart, selectSelectionEnd } from '../../../selectors';
import Prism from '../../../prism';
import type { Props } from './types';
import { isSpecialEvent, languageIsEqual, editorOptionsIsEqual } from './util';
import { prismSlug } from '../../../helpers';
import findOffset from './findOffset';

const elementToCursorMoveAction = (e: Element) =>
    editorCursorMoveAction([selectSelectionStart(e), selectSelectionEnd(e)]);

const mapToTargetCursorAction = (evt$: Observable<ProxyEvent>) =>
    evt$.map(e => elementToCursorMoveAction(e.target));

/**
 * Maps the Keydown event to an Action, or false if no relevant actions.
 *
 * @param {Event} evt - DOM Event object.
 * @returns {Action|false} Action to emit, or false if no actions.
 */
const mapKeydownToAction = (evt: ProxyEvent): Action => {
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

    throw new Error('Keydown is missing matching actions case');
};

/**
 * Resets the cursor's selection range.
 *
 * @param {Element} node - Node to add selection range to.
 * @param {number} ss - Selection start.
 * @param {number} se - Selection end.
 * @returns {Observable} Observable to update selection range.
 */
const setSelectionRange = (node: Element, ss: number, se: number): Observable<void> =>
    Kefir.stream(emitter => {
        const range = document.createRange();
        const offsetStart = findOffset(node, ss);
        let offsetEnd = offsetStart;

        if (se && se !== ss) {
            offsetEnd = findOffset(node, se);
        }

        if (offsetStart.error !== true && offsetEnd.error !== true) {
            range.setStart(offsetStart.element, offsetStart.offset);

            range.setEnd(offsetEnd.element, offsetEnd.offset);

            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
        }

        emitter.end();
    })
        .setName('setSelectionRange$');

/**
 * Highlight the given element with Prism.
 *
 * @param {Element} el - Element to highlight.
 * @returns {Observable} Observable to highlight element.
 */
const highlightElement = (el: Element): Observable<void> =>
    Kefir.stream(emitter => {
        Prism.highlightElement(el, false);
        emitter.end();
    })
        .setName('highlightElement$');

/**
 * Creates a stream to update Prism's setting and fetch any plugin scripts.
 *
 * @param {Object} props - Current pages props.
 * @returns {Observable} Observable that ends after Prism is updated.
 */
const createPrismUpdateStream = (props: Props): Observable<void> =>
    Kefir.fromPromise(Promise.all([
        Prism.setTheme(props.theme),
        Prism.togglePlugin('show-invisibles', props.invisibles === 'on')
    ]))
        .ignoreValues()
        .setName('prismUpdate$');


/**
 * Creates a stream to update the element after state updates.
 *
 * @param {Element} el - Element to render.
 * @param {Object} props - Latest props.
 * @returns {Observable} Observable to update element.
 */
const createDOMUpdateStream = (el: Element, props: Props): Observable<void> =>
    raf$.take(1).flatMap((): Observable<void> =>
        Kefir.concat([
            Kefir.stream(emitter => {
                el.textContent = props.code;
                emitter.end();
            }),
            highlightElement(el),
            props.cursor ? setSelectionRange(el, ...props.cursor) : Kefir.never()
        ])
    );

const Code = ({ stream$, onBlur, onClick, onFocus, onInput, onKeyUp, onKeyDown }, ref) => (
    <code
        onBlur={onBlur}
        onClick={onClick}
        onFocus={onFocus}
        onInput={onInput}
        onKeyUp={onKeyUp}
        onKeyDown={onKeyDown}
        className={stream$.thru(view((props: Props) => `language-${prismSlug(props.language)}`))}
        ref={ref} contentEditable="true" spellCheck="false">
    </code>
);

const refback = (ref$, { stream$ }: ObservableProps<Props>) => ref$.flatMap(el => {
    const keyUp$ = Kefir.fromEvents(el, 'keyup').setName('keyUp$');
    const keyDown$ = Kefir.fromEvents(el, 'keydown').setName('keyDown$');

    /**
     * Ensure the autoload path is set correctly on startup.
     */
    const setAutoloader$ = Kefir.stream(emitter => {
        Prism.setAutoloaderPath(__webpack_public_path__);
        emitter.end();
    })
        .setName('setAutoloader$');

    /**
     * Create initial render stream.
     *
     * This handles the render on pages load, making sure the editor
     * gets highlighted immediately. `stream$` is a Kefir.Property,
     * so we get a value immediately.
     */
    const initial$ = stream$.take(1)
        .flatMapLatest((props: Props): Observable<void> =>
            createDOMUpdateStream(el, props))
        .setName('initial$');

    /**
     * Create options update & render stream.
     *
     * This stream covers options changes & rerenders the editor.
     * There's no need to debounce or cancel because the user will
     * either be interacting with the options panel, so there's no
     * chance of messing up typing.
     */
    const options$ = stream$.skipDuplicates(editorOptionsIsEqual)
        .flatMapLatest((props: Props): Observable<void> =>
            Kefir.concat([
                createPrismUpdateStream(props),
                createDOMUpdateStream(el, props)
            ]))
        .setName('options$');

    /**
     * Create typing render stream.
     *
     * This stream ensures the rerenders don't take place while
     * the user is typing. We use a debounced keyup to ensure
     * the props
     */
    const typing$ = stream$.sampledBy(keyUp$.debounce(10))
        .skipDuplicates((prev, next) => prev.code === next.code)
        .flatMapLatest((props: Props): Observable<void> =>
            createDOMUpdateStream(el, props).takeUntilBy(keyDown$))
        .setName('typing$');

    /**
     * Create special keys renders stream.
     *
     * There are a few keys that run through the reducer logic. These
     * need to update the editor immediately, interrupting the user
     * typing to update the code in the editor and the cursor location.
     * The render is thus done synchronously.
     */
    const special$ = stream$.sampledBy(keyDown$.filter(isSpecialEvent).delay(0))
        .skipDuplicates((prev, next) => prev.code === next.code)
        .flatMapLatest((props: Props) =>
            raf$.take(1).flatMap((): Observable<void> =>
                createDOMUpdateStream(el, props)))
        .setName('special$');

    const language$ = stream$.skipDuplicates(languageIsEqual)
        .flatMapLatest((props: Props) => createDOMUpdateStream(el, props))
        .setName('language$');

    return Kefir.merge([
        setAutoloader$,
        initial$,
        options$,
        typing$,
        special$,
        language$
    ]);
});

export default toJunction({
    events: {
        onBlur: R.map(R.always(editorCursorMoveAction(false))),
        onClick: mapToTargetCursorAction,
        onFocus: mapToTargetCursorAction,
        onInput: R.map((evt: ProxyEvent) =>
            editorValueChangeAction({
                code: evt.target.textContent,
                cursor: [selectSelectionStart(evt.target), selectSelectionEnd(evt.target)]
            })
        ),
        onKeyUp: R.pipe(
            R.filter(R.pipe(isSpecialEvent, R.not)),
            mapToTargetCursorAction
        ),
        onKeyDown: R.pipe(
            R.filter(isSpecialEvent),
            R.map(mapKeydownToAction)
        )
    }
})(withRef$(Code, refback));
