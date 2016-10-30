import './index.scss';
import R from 'ramda';
import { fromPromise, merge, never, stream } from 'kefir';
import { editorIndentAction, editorMakeCommentAction, editorMakeNewlineAction,
    editorRedoAction, editorUndoAction, editorValueChangeAction } from '../../action';
import Prism from '../../prism';
import component from 'brookjs/component';
import render from 'brookjs/render';
import events from 'brookjs/events';
import { selectSelectionStart, selectSelectionEnd } from '../../selector';
import template from './index.hbs';

// const CRLF = /\r?\n|\r/g;

const updateLinenumber = (/* pre */) => stream(emitter => {
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
});

const mapKeydownToAction = evt => {
    const { altKey, shiftKey: inverse, metaKey, ctrlKey } = evt;
    const cmdOrCtrl = metaKey || ctrlKey;
    const { textContent: value } = evt.target;
    const ss = selectSelectionStart(evt.target);
    const se = selectSelectionEnd(evt.target);
    const length = ss === se ? 1 : Math.abs(se - ss);
    const start = se - length;
    const cursor = [ss, se];

    switch (evt.keyCode) {
        case 8: // Backspace
            const del = value.slice(start, se);

            return editorValueChangeAction({ value, cursor, del });
        case 9: // Tab
            if (!cmdOrCtrl) {
                return editorIndentAction({ value, cursor, inverse });
            }
            break;
        case 13:
            return editorMakeNewlineAction({ value, cursor });
        case 90:
            if (cmdOrCtrl) {
                return inverse ? editorRedoAction() : editorUndoAction();
            }
            break;
        case 191:
            if (cmdOrCtrl && !altKey) {
                return editorMakeCommentAction({ value, cursor });
            }
            break;
    }

    return false;
};

const filterCutEvent = evt => {
    const { selectionStart, selectionEnd } = evt.target;

    return selectionStart !== selectionEnd;
};

const mapCutEventToAction = evt => {
    const { selectionStart, selectionEnd, textContent } = evt.target;
    const selection = textContent.slice(selectionStart, selectionEnd);

    return editorValueChangeAction({
        value: textContent,
        add: '',
        del: selection,
        start: selectionStart
    });
};

const renderTemplate = render(template);

const setSelectionRange = (node, ss, se) => stream(emitter => {
    const loop = requestAnimationFrame(() => {
        let range = document.createRange();
        let offset = findOffset(node, ss);

        range.setStart(offset.element, offset.offset);

        if (se && se !== ss) {
            offset = findOffset(node, se);
        }

        range.setEnd(offset.element, offset.offset);

        let selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange(range);

        emitter.end();
    });

    return () => cancelAnimationFrame(loop);
});

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
                var len = element.textContent.length;

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

const highlightElement = el => stream(emitter => {
    let loop = requestAnimationFrame(() => {
        Prism.highlightElement(el.querySelector('code'), false);
        emitter.end();
    });

    return () => cancelAnimationFrame(loop);
});

export default component({
    onMount: () => stream(emitter => {
        Prism.setAutoloaderPath(__webpack_public_path__);

        emitter.end();
    }),
    render: R.curry((el, prev, next) => fromPromise(Promise.all([
        Prism.setTheme(next.editor.theme),
        Prism.togglePlugin('show-invisibles', next.editor.invisibles === 'on')
    ]))
        .ignoreValues()
        .concat(merge([
            renderTemplate(el, prev, next),
            highlightElement(el),
            next.instance.cursor ? setSelectionRange(el.querySelector('code'), ...next.instance.cursor) : never(),
            next.instance.cursor ? updateLinenumber(el.querySelector('pre')) : never()
        ]))
    ),
    events: events({
        onClick: event$ => event$.flatMap(R.pipe(
            R.prop('target'),
            updateLinenumber
        )),
        onKeydown: R.pipe(
            events$ => events$.debounce(0),
            R.map(mapKeydownToAction),
            R.filter(R.identity)
        ),
        onKeypress: event$ => event$
            .debounce(0)
            .filter(evt => evt.charCode && !(evt.metaKey || evt.ctrlKey))
            .map(evt => {
                const pre = evt.target;
                const start = selectSelectionStart(pre);
                const end = selectSelectionEnd(pre);
                const add = String.fromCharCode(evt.charCode);
                const value = pre.textContent;
                const del = start === end ? '' : pre.textContent.slice(start, end);
                const cursor = [start, end];

                return editorValueChangeAction({ value, cursor, add, del });
            }),
        onCut: R.pipe(
            events$ => events$.debounce(0),
            R.filter(filterCutEvent),
            R.map(mapCutEventToAction)
        ),
        onPaste: event$ => event$.flatMap(evt => stream(emitter => {
            const pre = evt.target;
            let ss = selectSelectionStart(pre);
            let se = selectSelectionEnd(pre);
            const selection = ss === se ? '' : pre.textContent.slice(ss, se);

            if (evt.clipboardData) {
                evt.preventDefault();

                let pasted = evt.clipboardData.getData('text/plain');

                document.execCommand('insertText', false, pasted);

                emitter.value(editorValueChangeAction({
                    value: pre.textContent,
                    add: pasted,
                    del: selection,
                    cursor: [ss, se]
                }));

                ss += pasted.length;

                setSelectionRange(pre, ss, ss);

                pre.onkeyup();

                emitter.end();
            } else {
                setTimeout(function() {
                    const newse = selectSelectionEnd(pre);
                    let pasted = pre.textContent.slice(ss, newse);

                    emitter.value(editorValueChangeAction({
                        value: pre.textContent,
                        add: pasted,
                        del: selection,
                        cursor: [ss, newse]
                    }));

                    ss += pasted.length;

                    setSelectionRange(pre, ss, ss);

                    pre.onkeyup();

                    emitter.end();
                }, 10);
            }
        }))
    })
});
