import './index.scss';
import R from 'ramda';
import { fromPromise, merge, never, stream } from 'kefir';
import { editorIndentAction, editorMakeCommentAction, editorMakeNewlineAction,
    editorRedoAction, editorUndoAction, editorValueChangeAction } from '../../action';
import Prism from '../../prism';
import component from 'brookjs/component';
import render from 'brookjs/render';
import events from 'brookjs/events';
import template from './index.hbs';

const CRLF = /\r?\n|\r/g;

const keyupIgnore = [
    9, 91, 93, 16, 17, 18, // modifiers
    20, // caps lock
    13, // Enter (handled by keydown)
    112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, // F[0-12]
    27 // Esc
];

const updateLinenumber = pre => stream(emitter => {
    let content = pre.textContent;
    let ss = pre.selectionStart;
    // let se = pre.selectionEnd;
    //
    // // @todo push into store
    // ss && pre.setAttribute('data-ss', ss);
    // se && pre.setAttribute('data-se', se);

    // Update current line highlight
    let line = (content.slice(0, ss).match(CRLF) || []).length;

    pre.setAttribute('data-line', line + 1);

    emitter.end();
});

const mapKeydownToAction = evt => {
    const { altKey, shiftKey: inverse, metaKey, ctrlKey } = evt;
    const cmdOrCtrl = metaKey || ctrlKey;
    const { selectionStart: ss, selectionEnd: se, textContent: value } = evt.target;
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

export default component({
    onMount: () => stream(emitter => {
        Prism.setAutoloaderPath(__webpack_public_path__);

        emitter.end();
    }),
    render: R.curry((el, prev, next) => fromPromise(Promise.all([
        Prism.setTheme(next.editor.theme),
        Prism.togglePlugin('line-highlight', true),
        Prism.togglePlugin('show-invisibles', next.editor.invisibles === 'on')
    ]))
        .ignoreValues()
        .concat(merge([renderTemplate(el, prev, next), stream(emitter => {
            let loop = requestAnimationFrame(() => {
                Prism.highlightElement(el.querySelector('code'), false);
                emitter.end();
            });

            return () => cancelAnimationFrame(loop);
        })]))
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
        onKeyup: event$ => event$.flatMap(evt => {
            let pre = evt.target;
            let keyCode = evt.keyCode || 0;
            let code = pre.textContent;
            let lineNumber$ = never();

            if (keyCode < 9 || keyCode === 13 || keyCode > 32 && keyCode < 41) {
                lineNumber$ = updateLinenumber(pre);
            }

            if (keyupIgnore.indexOf(keyCode) > -1) {
                return lineNumber$;
            }

            return lineNumber$.merge(stream(emitter => {
                if (keyCode !== 37 && keyCode !== 39) {
                    let ss = pre.selectionStart;
                    let se = pre.selectionEnd;

                    Prism.highlightElement(pre);

                    // Dirty fix to #2
                    if (!/\n$/.test(code)) {
                        pre.innerHTML = pre.innerHTML + '\n';
                    }

                    if (ss !== null || se !== null) {
                        pre.setSelectionRange(ss, se);
                    }
                }

                emitter.end();
            }));
        }),
        onKeypress: event$ => event$
            .debounce(0)
            .filter(evt => evt.charCode && !(evt.metaKey || evt.ctrlKey))
            .map(evt => {
                const pre = evt.target;
                const start = pre.selectionStart;
                const end = pre.selectionEnd;
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
            let pre = evt.target;
            let ss = pre.selectionStart;
            let se = pre.selectionEnd;
            let selection = ss === se ? '' : pre.textContent.slice(ss, se);

            if (evt.clipboardData) {
                evt.preventDefault();

                let pasted = evt.clipboardData.getData('text/plain');

                document.execCommand('insertText', false, pasted);

                emitter.value(editorValueChangeAction({
                    value: pre.textContent,
                    add: pasted,
                    del: selection,
                    start: ss
                }));

                ss += pasted.length;

                pre.setSelectionRange(ss, ss);

                pre.onkeyup();

                emitter.end();
            } else {
                setTimeout(function() {
                    let newse = pre.selectionEnd;

                    let pasted = pre.textContent.slice(ss, newse);

                    emitter.value(editorValueChangeAction({
                        value: pre.textContent,
                        add: pasted,
                        del: selection,
                        start: ss
                    }));

                    ss += pasted.length;

                    pre.setSelectionRange(ss, ss);

                    pre.onkeyup();

                    emitter.end();
                }, 10);
            }
        }))
    })
});
