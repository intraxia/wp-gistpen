import Kefir, { Observable } from 'kefir';
import { raf$, toJunction, withRef$, Refback, ofType, Maybe } from 'brookjs';
import React, { forwardRef } from 'react';
import { isActionOf } from 'typesafe-actions';
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
  findOffset,
} from './dom';
import { TAB, ENTER, Z, FORWARD_SLASH } from './keyCodes';

type Props = {
  Prism: PrismLib;
  code: string;
  cursor: Cursor;
  language: string;
  lineNumbers?: boolean;
  highlight?: Maybe<string>;
  offset?: Maybe<number>;
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
    case TAB:
      evt.stopPropagation();
      return Kefir.constant(editorIndent({ code, cursor, inverse }));
    case ENTER:
      return Kefir.constant(editorMakeNewline({ code, cursor }));
    case Z:
      return Kefir.constant(inverse ? editorRedo() : editorUndo());
    case FORWARD_SLASH:
      return Kefir.constant(editorMakeComment({ code, cursor }));
  }

  return Kefir.never();
};

const setSelectionRange = (node: Element, cursor: Cursor) =>
  Kefir.stream<never, never>(emitter => {
    if (node !== document.activeElement || cursor == null) {
      return emitter.end();
    }

    const [ss, se] = cursor;

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

const domUpdate$ = (el: Element, props: Props) =>
  raf$.take(1).flatMap(() => {
    const code = el.querySelector('code')!;

    return Kefir.concat<never, never>([
      Kefir.stream(emitter => {
        const text = props.code === '' ? '\n' : props.code;

        if (code.textContent !== text) {
          code.textContent = text;
        }

        emitter.end();
      }),
      highlightElement(code, props.Prism),
      setSelectionRange(code, props.cursor),
    ]);
  });

const codeStyles: React.CSSProperties = {
  outline: 'none',
};

const Code: React.ForwardRefRenderFunction<HTMLPreElement, Props> = (
  {
    language,
    lineNumbers = true,
    highlight,
    offset,
    onBlur,
    onClick,
    onFocus,
    onInput,
    onKeyUp,
    onKeyDown,
  },
  ref,
) => (
  <pre
    className={`language-${language} ${lineNumbers ? `line-numbers` : ''}`}
    data-line={highlight || null}
    data-line-offset={offset || null}
    // compare vs null/undefined gives us "correct" answers
    data-start={offset! > 0 ? offset : null}
    spellCheck={false}
    ref={ref}
  >
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
      contentEditable
      spellCheck={false}
      tabIndex={0}
    />
  </pre>
);

const refback: Refback<Props, HTMLPreElement, EditorAction> = (ref$, props$) =>
  ref$.flatMap(el =>
    props$.flatMapLatest(props =>
      // schedule render unless user types.
      domUpdate$(el, props).takeUntilBy(Kefir.fromEvents(el, 'keydown')),
    ),
  );

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
  onKeyUp: (evt$: Observable<React.KeyboardEvent<HTMLElement>, never>) =>
    evt$.filter(e => !isSpecialEvent(e)).thru(mapToTargetCursorAction()),
  onKeyDown: (evt$: Observable<React.KeyboardEvent<HTMLElement>, never>) =>
    evt$.filter(e => isSpecialEvent(e)).flatMap(mapKeydownToAction),
};

export default toJunction(events, evt$ => {
  const cursorMove$ = evt$
    .thru(ofType(editorCursorMove))
    // debounce cursor move to the next raf
    .flatMapLatest(action => raf$.take(1).map(() => action));

  const notCursorMove$ = evt$.filter(
    action => !isActionOf(editorCursorMove, action),
  );

  return Kefir.merge([cursorMove$, notCursorMove$]);
})(withRef$(refback)(forwardRef(Code)));
