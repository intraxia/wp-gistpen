import R from 'ramda';
import { concat, fromEvents, fromPromise, merge, never, stream } from 'kefir';
import Prism from '../../../prism';
import toolbarStyles from 'prismjs/plugins/toolbar/prism-toolbar.css';
import { renderFromHTML, raf$ } from 'brookjs';
import { editorOptionsIsEqual, lineNumberIsEqual, isSpecialEvent } from './util';
import template from './index.hbs';

toolbarStyles.use();

// const CRLF = /\r?\n|\r/g;

/**
 * Update the highlighted line numbers next to the editor.
 *
 * @returns {Observable} Observable to update the editor line numbers.
 */
function updateLineNumber(/*pre, start, end*/) {
    return stream(emitter => {
        // let content = pre.textContent;
        // let ss = pre.selectionStart;
        // let se = pre.selectionEnd;
        //
        // // @todo push into store
        // ss && pre.setAttribute('data-ss', ss);
        // se && pre.setAttribute('data-se', se);

        // Update current line highlight
        // let line = (content.slice(0, ss).match(CRLF) || []).length;

        // pre.setAttribute('data-line', line + 1);

        emitter.end();
    })
        .setName('UpdateLineNumbers$');
}

/**
 * Resets the cursor's selection range.
 *
 * @param {Element} node - Node to add selection range to.
 * @param {number} ss - Selection start.
 * @param {number} se - Selection end.
 * @returns {Observable} Observable to update selection range.
 */
const setSelectionRange = R.curry(function setSelectionRange(node, ss, se) {
    return stream(emitter => {
        const range = document.createRange();
        let offset = findOffset(node, ss);

        range.setStart(offset.element, offset.offset);

        if (se && se !== ss) {
            offset = findOffset(node, se);
        }

        range.setEnd(offset.element, offset.offset);

        const selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange(range);

        emitter.end();
    })
        .setName('SetSelectionRange$');
});

/**
 * Find the offset for a given selection start
 *
 * @param {Element} root - Element to find offset for.
 * @param {number} ss - Start point for offset.
 * @returns {Object} Offset information.
 */
function findOffset(root, ss) {
    let container;

    if (!root) {
        return null;
    }

    let offset = 0;
    let element = root;

    do {
        container = element;
        element = element.firstChild;

        if (element) {
            do {
                let len = element.textContent.length;

                if (offset <= ss && offset + len > ss) {
                    break;
                }

                offset += len;
            } while (element = element.nextSibling);
        }

        if (!element) {
            // It's the container's lastChild
            break;
        }
    } while (element && element.hasChildNodes() && element.nodeType !== 3);

    if (element) {
        return {
            element: element,
            offset: ss - offset
        };
    } else if (container) {
        element = container;

        while (element && element.lastChild) {
            element = element.lastChild;
        }

        if (element.nodeType === 3) {
            return {
                element: element,
                offset: element.textContent.length
            };
        } else {
            return {
                element: element,
                offset: 0
            };
        }
    }

    return {
        element: root,
        offset: 0,
        error: true
    };
}

/**
 * Highlight the given element with Prism.
 *
 * @param {Element} el - Element to highlight.
 * @returns {Observable} Observable to highlight element.
 */
const highlightElement = function highlightElement(el) {
    return stream(emitter => {
        Prism.highlightElement(el.querySelector('code'), false);
        emitter.end();
    })
        .setName('HighlightElement$');
};

/**
 * Creates a stream to update Prism's setting and fetch any plugin scripts.
 *
 * @param {Object} state - Current page state.
 * @returns {Observable} Observable that ends after Prism is updated.
 */
const createPrismUpdateStream = function createPrismUpdateStream(state) {
    return fromPromise(Promise.all([
        Prism.setTheme(state.editor.theme),
        Prism.togglePlugin('show-invisibles', state.editor.invisibles === 'on')
    ]))
        .ignoreValues()
        .setName('PrismUpdateStream');
};

/**
 * Creates a stream to update the element after state updates.
 *
 * @param {Element} el - Element to render.
 * @param {Object} props - Latest props.
 * @returns {Observable} Observable to update element.
 */
const createDOMUpdateStream = R.curry(function createDOMUpdateStream(el, props) {
    let stream$ = renderFromHTML(el, template(props)).concat(highlightElement(el));

    if (props.instance.cursor) {
        stream$ = stream$.concat(setSelectionRange(el.querySelector('code'), ...props.instance.cursor));
    }

    return stream$.setName('DOMUpdateStream');
});

/**
 * Creates a new stream on mount that handles updating Prism & rendering the editor.
 *
 * This gives us finer control over the render cycle than merely deferring to `brookjs`,
 * as we don't just want to rerender on state changes but when the keyup event occurs,
 * allowing us to rerender the highlighting between typings, rather than interrupting
 * the user.
 *
 * @param {Element} el - Editor element.
 * @param {Observable} props$ - Stream of editor props.
 * @returns {Observable} - Stream of renders.
 */
export default R.curry(function onMount(el, props$) {
    const keyUp$ = fromEvents(el, 'keyup').setName('KeyUp$');
    const keyDown$ = fromEvents(el, 'keydown').setName('KeyDown$');

    // Ensure the autoload path is set correctly on startup.
    // @todo move elsewhere?
    const setAutoloader$ = stream(emitter => {
        Prism.setAutoloaderPath(__webpack_public_path__);

        emitter.end();
    })
        .setName('SetAutoloader$');

    /**
     * Create initial render stream.
     *
     * This handles the render on page load, making sure the editor
     * gets highlighted immediately. `props$` is a Kefir.Property,
     * so we get a value immediately.
     */
    const initial$ = props$.take(1)
        .flatMapLatest(props => createDOMUpdateStream(el, props))
        .setName('Initial$');

    /**
     * Create options update & render stream.
     *
     * This stream covers options changes & rerenders the editor.
     * There's no need to debounce or cancel because the user will
     * either be interacting with the options panel, so there's no
     * chance of messing up typing.
     */
    const options$ = props$.skipDuplicates(editorOptionsIsEqual)
        .flatMapLatest(props => concat([
            createPrismUpdateStream(props),
            createDOMUpdateStream(el, props)
        ]))
        .setName('Options$');

    /**
     * Create typing render stream.
     *
     * This stream ensures the rerenders don't take place while
     * the user is typing. We use a debounced keyup to ensure
     * the props
     */
    const typing$ = props$.sampledBy(keyUp$.debounce(10))
        .flatMapLatest(props => createDOMUpdateStream(el, props).takeUntilBy(keyDown$))
        .setName('Typing$');

    /**
     * Create special keys renders stream.
     *
     * There are a few keys that run through the reducer logic. These
     * need to update the editor immediately, interrupting the user
     * typing to update the code in the editor and the cursor location.
     * The render is thus done synchronously.
     */
    const special$ = props$.sampledBy(keyDown$.filter(isSpecialEvent).delay(0))
        .flatMapLatest(props => raf$.take(1).flatMap(() =>  {
            const code = el.querySelector('code');

            return stream(emitter => {
                code.textContent = props.instance.code;
                emitter.end();
            })
                .concat(highlightElement(el))
                .concat(props.instance.cursor ? setSelectionRange(code, ...props.instance.cursor) : never());
        }))
        .setName('Special$');

    /**
     * Create line number render stream.
     *
     * Update the line numbers as soon as they change. This
     * doesn't need to be affected by the typing, as this
     * won't change anything in the DOM that the editor
     * interacts with.
     */
    const lineNumber$ = props$.skipDuplicates(lineNumberIsEqual)
        .filter(R.path(['instance', 'cursor']))
        .flatMapLatest(props => updateLineNumber(el.querySelector('pre'), ...props.instance.cursor))
        .setName('LineNumbers$');

    return merge([
        setAutoloader$,
        initial$,
        options$,
        typing$,
        special$,
        lineNumber$
    ])
        .setName('OnMount$');
});
