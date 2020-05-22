import Kefir, { Observable } from 'kefir';
import { raf$, toJunction, withRef$, Refback } from 'brookjs';
import React, { forwardRef, memo } from 'react';
import { PrismLib, Cursor, EditorAction } from './types';
import {
  editorCursorMove,
  editorIndent,
  editorMakeComment,
  editorMakeNewline,
  editorRedo,
  editorUndo,
  editorValueChange,
} from './actions';
import {
  selectSelectionStart,
  selectSelectionEnd,
  isSpecialEvent,
  languageIsEqual,
  findOffset,
} from './dom';

type Props = {
  Prism: PrismLib;
  code: string;
  cursor: Cursor;
  language: string;
  onBlur: (e: React.FocusEvent<HTMLElement>) => void;
  onClick: (e: React.MouseEvent<HTMLElement>) => void;
  onFocus: (e: React.FocusEvent<HTMLElement>) => void;
  onInput: (e: React.ChangeEvent<HTMLElement>) => void;
  onKeyUp: (e: React.KeyboardEvent<HTMLElement>) => void;
  onKeyDown: (e: React.KeyboardEvent<HTMLElement>) => void;
};

const elementToCursorMoveAction = (e: Element) =>
  editorCursorMove([selectSelectionStart(e), selectSelectionEnd(e)]);

// Use this to fill in type at call site.
const mapToTargetCursorAction = <
  E extends React.SyntheticEvent<HTMLElement>
>() => (evt$: Observable<E, never>) =>
  evt$.map(e => elementToCursorMoveAction(e.target as Element));

const mapKeydownToAction = (
  evt: React.KeyboardEvent<HTMLElement>,
): Observable<EditorAction, never> => {
  const { shiftKey: inverse } = evt;
  let { textContent: code } = evt.target as HTMLElement;
  code = code ?? '';
  const cursor: Cursor = [
    selectSelectionStart(evt.target as Element),
    selectSelectionEnd(evt.target as Element),
  ];

  evt.preventDefault();

  switch (evt.keyCode) {
    case 9: // Tab
      return Kefir.constant(editorIndent({ code, cursor, inverse }));
    case 13:
      return Kefir.constant(editorMakeNewline({ code, cursor }));
    case 90:
      return Kefir.constant(inverse ? editorRedo() : editorUndo());
    case 191:
      return Kefir.constant(editorMakeComment({ code, cursor }));
  }

  return Kefir.never();
};

const setSelectionRange = (node: Element, ss: number, se: number) =>
  Kefir.stream<never, never>(emitter => {
    if (ss === selectSelectionStart(node) && se === selectSelectionEnd(node)) {
      return emitter.end();
    }

    const range = document.createRange();
    const offsetStart = findOffset(node, ss);
    let offsetEnd = offsetStart;

    if (se && se !== ss) {
      offsetEnd = findOffset(node, se);
    }

    if (offsetStart.error == null && offsetEnd.error == null) {
      range.setStart(offsetStart.element, offsetStart.offset);

      range.setEnd(offsetEnd.element, offsetEnd.offset);

      const selection = window.getSelection();
      if (selection != null) {
        selection.removeAllRanges();
        selection.addRange(range);
      }
    }

    emitter.end();
  }).setName('setSelectionRange$');

const highlightElement = (el: Element, Prism: PrismLib) =>
  Kefir.stream<never, never>(emitter => {
    Prism.highlightElement(el, false);
    emitter.end();
  }).setName('highlightElement$');

const createDOMUpdateStream = (el: Element, props: Props) =>
  raf$.take(1).flatMap(() =>
    Kefir.concat<never, never>([
      Kefir.stream(emitter => {
        el.textContent = props.code;
        emitter.end();
      }),
      highlightElement(el, props.Prism),
      props.cursor
        ? setSelectionRange(el, props.cursor[0], props.cursor[1])
        : Kefir.never(),
    ]),
  );

const codeStyles: React.CSSProperties = {
  outline: 'none',
};

const Code: React.ForwardRefRenderFunction<HTMLElement, Props> = (
  { language, onBlur, onClick, onFocus, onInput, onKeyUp, onKeyDown },
  ref,
) => (
  <code
    data-testid="editor-code"
    style={codeStyles}
    onBlur={onBlur}
    onClick={onClick}
    onFocus={onFocus}
    onInput={onInput}
    onKeyUp={onKeyUp}
    onKeyDown={onKeyDown}
    className={`language-${language}`}
    ref={ref}
    contentEditable
    spellCheck={false}
    tabIndex={0}
  />
);

const refback: Refback<Props, HTMLElement, EditorAction> = (ref$, props$) =>
  ref$.flatMap(el => {
    const keyUp$ = Kefir.fromEvents<KeyboardEvent, never>(el, 'keyup').setName(
      'keyUp$',
    );
    const keyDown$ = Kefir.fromEvents<KeyboardEvent, never>(
      el,
      'keydown',
    ).setName('keyDown$');

    /**
     * Create initial render stream.
     *
     * This handles the render on pages load, making sure the editor
     * gets highlighted immediately. `props$` is a Kefir.Property,
     * so we get a value immediately.
     */
    const initial$ = props$
      .take(1)
      .flatMapLatest(props => createDOMUpdateStream(el, props))
      .setName('initial$');

    /**
     * Create typing render stream.
     *
     * This stream ensures the rerenders don't take place while
     * the user is typing. We use a debounced keyup to ensure
     * the props are up-to-date.
     */
    const typing$ = props$
      .sampledBy(keyUp$.debounce(10))
      .skipDuplicates((prev, next) => prev.code === next.code)
      .flatMapLatest(props =>
        createDOMUpdateStream(el, props).takeUntilBy(keyDown$),
      )
      .setName('typing$');

    /**
     * Create special keys render stream.
     *
     * There are a few keys that run through the reducer logic. These
     * need to update the editor immediately, interrupting the user
     * typing to update the code in the editor and the cursor location.
     * The render is thus done immediately.
     */
    const special$ = props$
      .sampledBy(keyDown$.filter(isSpecialEvent).delay(0))
      .skipDuplicates((prev, next) => prev.code === next.code)
      .flatMapLatest(props => createDOMUpdateStream(el, props))
      .setName('special$');

    /**
     * Create language render stream.
     *
     * This needs to update the DOM every time the language changes.
     */
    const language$ = props$
      .skipDuplicates(languageIsEqual)
      .flatMapLatest(props => createDOMUpdateStream(el, props))
      .setName('language$');

    return Kefir.merge<EditorAction, never>([
      initial$,
      typing$,
      special$,
      language$,
    ]);
  });

const events = {
  onBlur: (evt$: Observable<React.FocusEvent<HTMLElement>, never>) =>
    evt$.map(() => editorCursorMove(null)),
  onClick: mapToTargetCursorAction<React.MouseEvent<HTMLElement>>(),
  onFocus: mapToTargetCursorAction<React.FocusEvent<HTMLElement>>(),
  onInput: (evt$: Observable<React.ChangeEvent<HTMLElement>, never>) =>
    evt$.map(evt =>
      editorValueChange({
        code: evt.target.textContent ?? '',
        cursor: [
          selectSelectionStart(evt.target),
          selectSelectionEnd(evt.target),
        ],
      }),
    ),
  onKeyUp: (evt$: Observable<React.KeyboardEvent<HTMLPreElement>, never>) =>
    evt$.filter(e => !isSpecialEvent(e)).thru(mapToTargetCursorAction()),
  onKeyDown: (evt$: Observable<React.KeyboardEvent<HTMLPreElement>, never>) =>
    evt$.filter(e => isSpecialEvent(e)).flatMap(mapKeydownToAction),
};

export default toJunction(events)(
  withRef$(refback)(
    memo(forwardRef(Code), (a, b) => a.language === b.language),
  ),
);
