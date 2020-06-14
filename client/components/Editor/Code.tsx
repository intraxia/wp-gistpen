import Kefir, { Observable } from 'kefir';
import { raf$, toJunction, withRef$, Refback } from 'brookjs';
import React, { memo, forwardRef } from 'react';
import Prism from 'prismjs';
import {
  editorCursorMove,
  editorIndent,
  editorMakeComment,
  editorMakeNewline,
  editorRedo,
  editorUndo,
  editorValueChange,
} from '../../actions';
import { selectSelectionStart, selectSelectionEnd } from '../../selectors';
import { prismSlug } from '../../helpers';
import { RootAction, Cursor } from '../../util';
import { setTheme, togglePlugin } from '../../prism';
import { Toggle } from '../../snippet';
import { isSpecialEvent, languageIsEqual, editorOptionsIsEqual } from './util';
import findOffset from './findOffset';

type Props = {
  code: string;
  cursor: Cursor;
  language: string;
  theme: string;
  invisibles: Toggle;
  onBlur: (e: React.FocusEvent<HTMLElement>) => void;
  onClick: (e: React.MouseEvent<HTMLElement>) => void;
  onFocus: (e: React.FocusEvent<HTMLElement>) => void;
  onInput: (e: React.ChangeEvent<HTMLElement>) => void;
  onKeyUp: (e: React.KeyboardEvent<HTMLElement>) => void;
  onKeyDown: (e: React.KeyboardEvent<HTMLElement>) => void;
};

const elementToCursorMoveAction = (e: Element) =>
  editorCursorMove([selectSelectionStart(e), selectSelectionEnd(e)], null);

// Use this to fill in type at call site.
const mapToTargetCursorAction = <
  E extends React.SyntheticEvent<HTMLElement>
>() => (evt$: Observable<E, never>) =>
  evt$.map(e => elementToCursorMoveAction(e.target as Element));

const mapKeydownToAction = (
  evt: React.KeyboardEvent<HTMLElement>,
): RootAction => {
  const { shiftKey: inverse } = evt;
  let { textContent: code } = evt.target as HTMLElement;
  code = code || '';
  const cursor: Cursor = [
    selectSelectionStart(evt.target as Element),
    selectSelectionEnd(evt.target as Element),
  ];

  evt.preventDefault();

  switch (evt.keyCode) {
    case 9: // Tab
      return editorIndent({ code, cursor, inverse }, null);
    case 13:
      return editorMakeNewline({ code, cursor }, null);
    case 90:
      return inverse ? editorRedo() : editorUndo();
    case 191:
      return editorMakeComment({ code, cursor });
  }

  throw new Error('Keydown is missing matching actions case');
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

const highlightElement = (el: Element) =>
  Kefir.stream<never, never>(emitter => {
    Prism.highlightElement(el, false);
    emitter.end();
  }).setName('highlightElement$');

let init = false;

const createPrismUpdateStream = (props: Props) =>
  // Since we're igonring the values anyway, I don't mind casting to `any`.
  Kefir.fromPromise<any, never>(
    Promise.all([
      setTheme(props.theme),
      togglePlugin('line-numbers', true).then(() => {
        // We only need to register this callback once, but only after the
        // plugin has been loaded once.
        if (!init) {
          init = true;
          Prism.hooks.add('line-numbers', env => {
            const code = env.element;
            const pre = code?.parentNode as HTMLPreElement | null;

            if (pre == null || code == null) {
              return;
            }

            const incoming = code.querySelector('.line-numbers-rows');
            const outgoings = pre.querySelectorAll('.line-numbers-rows');

            for (let i = 0; i < outgoings.length; i++) {
              const outgoing = outgoings[i];

              if (outgoing !== incoming) {
                outgoing.remove();
              }
            }

            incoming != null && pre.appendChild(incoming);
          });
        }
      }),
      togglePlugin('show-invisibles', props.invisibles === 'on'),
    ]),
  )
    .ignoreValues()
    .setName('prismUpdate$');

const createDOMUpdateStream = (el: Element, props: Props) =>
  raf$.take(1).flatMap(() =>
    Kefir.concat<never, never>([
      Kefir.stream(emitter => {
        el.textContent = props.code;
        emitter.end();
      }),
      highlightElement(el),
      props.cursor
        ? setSelectionRange(el, props.cursor[0], props.cursor[1])
        : Kefir.never(),
    ]),
  );

const Code: React.RefForwardingComponent<HTMLElement, Props> = (
  { language, onBlur, onClick, onFocus, onInput, onKeyUp, onKeyDown },
  ref,
) => (
  <code
    onBlur={onBlur}
    onClick={onClick}
    onFocus={onFocus}
    onInput={onInput}
    onKeyUp={onKeyUp}
    onKeyDown={onKeyDown}
    className={`language-${prismSlug(language)}`}
    ref={ref}
    contentEditable
    spellCheck={false}
  />
);

const refback: Refback<Props, HTMLElement, RootAction> = (ref$, props$) =>
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
     * Create options update & render stream.
     *
     * This stream covers options changes & rerenders the editor.
     * There's no need to debounce or cancel because the user will
     * either be interacting with the options panel, so there's no
     * chance of messing up typing.
     */
    const options$ = props$
      .skipDuplicates(editorOptionsIsEqual)
      .flatMapLatest(props =>
        Kefir.concat([
          createPrismUpdateStream(props),
          createDOMUpdateStream(el, props),
        ]),
      )
      .setName('options$');

    /**
     * Create typing render stream.
     *
     * This stream ensures the rerenders don't take place while
     * the user is typing. We use a debounced keyup to ensure
     * the props
     */
    const typing$ = props$
      .sampledBy(keyUp$.debounce(10))
      .skipDuplicates((prev, next) => prev.code === next.code)
      .flatMapLatest(props =>
        createDOMUpdateStream(el, props).takeUntilBy(keyDown$),
      )
      .setName('typing$');

    /**
     * Create special keys renders stream.
     *
     * There are a few keys that run through the reducer logic. These
     * need to update the editor immediately, interrupting the user
     * typing to update the code in the editor and the cursor location.
     * The render is thus done synchronously.
     */
    const special$ = props$
      .sampledBy(keyDown$.filter(isSpecialEvent).delay(0))
      .skipDuplicates((prev, next) => prev.code === next.code)
      .flatMapLatest(props =>
        raf$.take(1).flatMap(() => createDOMUpdateStream(el, props)),
      )
      .setName('special$');

    const language$ = props$
      .skipDuplicates(languageIsEqual)
      .flatMapLatest(props => createDOMUpdateStream(el, props))
      .setName('language$');

    return Kefir.merge<RootAction, never>([
      initial$,
      options$,
      typing$,
      special$,
      language$,
    ]);
  });

const events = {
  onBlur: (evt$: Observable<React.FocusEvent<HTMLElement>, never>) =>
    evt$.map(() => editorCursorMove(false, null)),
  onClick: mapToTargetCursorAction<React.MouseEvent<HTMLElement>>(),
  onFocus: mapToTargetCursorAction<React.FocusEvent<HTMLElement>>(),
  onInput: (evt$: Observable<React.ChangeEvent<HTMLElement>, never>) =>
    evt$.map(evt =>
      editorValueChange(
        {
          code: (evt.target as HTMLElement).textContent || '',
          cursor: [
            selectSelectionStart(evt.target as Element),
            selectSelectionEnd(evt.target as Element),
          ],
        },
        null,
      ),
    ),
  onKeyUp: (evt$: Observable<React.KeyboardEvent<HTMLPreElement>, never>) =>
    evt$.filter(e => !isSpecialEvent(e)).thru(mapToTargetCursorAction()),
  onKeyDown: (evt$: Observable<React.KeyboardEvent<HTMLPreElement>, never>) =>
    evt$.filter(e => isSpecialEvent(e)).map(mapKeydownToAction),
};

export default toJunction(events)(
  withRef$(refback)(
    memo(
      forwardRef(Code),
      (prev: Props, next: Props) => prev.language === next.language,
    ),
  ),
);
