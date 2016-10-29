import './index.scss';
import R from 'ramda';
import { fromPromise, merge, never, stream } from 'kefir';
import Prism from '../../prism';
import component from 'brookjs/component';
import events from 'brookjs/events';

const CRLF = /\r?\n|\r/g;

const keyupIgnore = [
    9, 91, 93, 16, 17, 18, // modifiers
    20, // caps lock
    13, // Enter (handled by keydown)
    112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, // F[0-12]
    27 // Esc
];

const changeAction = state => ({
    type: 'CHANGE_ACTION',
    payload: { state }
});

const commentAction = R.always({
    type: 'COMMENT_ACTION'
});

const indentAction = inverse => ({
    type: 'INDENT_ACTION',
    payload: { inverse }
});

const newlineAction = R.always({
    type: 'NEWLINE_ACTION'
});

const undoAction = R.always({
    type: 'UNDO_ACTION'
});

const redoAction = R.always({
    type: 'REDO_ACTION'
});

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

const createSettingsRenderStream = (pre, props$) => props$.skipDuplicates((prev, next) =>
        prev.editor.theme === next.editor.theme && prev.editor.invisibles === next.editor.invisibles)
    .flatMapLatest(props => fromPromise(Promise.all([
        Prism.setTheme(props.editor.theme),
        Prism.togglePlugin('line-highlight', true),
        Prism.togglePlugin('show-invisibles', props.editor.invisibles)
    ]))
        .flatMapLatest(() => stream(emitter => {
            let loop = requestAnimationFrame(() => {
                Prism.highlightElement(pre);
                emitter.end();
            });

            return () => cancelAnimationFrame(loop);
        })));

export default component({
    onMount: R.curry((el, props$) => {
        const pre = el.querySelector('pre');
        Prism.setAutoloaderPath(__webpack_public_path__);

        return merge([updateLinenumber(pre), createSettingsRenderStream(pre, props$)]);
    }),
    events: events({
        onClick: event$ => event$.flatMap(R.pipe(
            R.prop('target'),
            updateLinenumber
        )),
        onKeydown: event$ => event$.flatMap(evt => stream(emitter => {
            let cmdOrCtrl = evt.metaKey || evt.ctrlKey;
            let pre = evt.target;

            switch (evt.keyCode) {
                case 8: // Backspace
                    let ss = pre.selectionStart;
                    let se = pre.selectionEnd;
                    let length = ss === se ? 1 : Math.abs(se - ss);
                    let start = se - length;

                    emitter.value(changeAction({
                        add: '',
                        del: pre.textContent.slice(start, se),
                        start: start
                    }));
                    break;
                case 9: // Tab
                    if (!cmdOrCtrl) {
                        emitter.value(indentAction(evt.shiftKey));
                    }
                    break;
                case 13:
                    emitter.value(newlineAction());
                    break;
                case 90:
                    if (cmdOrCtrl) {
                        emitter.value(evt.shiftKey ? redoAction() : undoAction());
                    }
                    break;
                case 191:
                    if (cmdOrCtrl && !evt.altKey) {
                        emitter.value(commentAction());
                    }
                    break;
            }

            emitter.end();
        })),
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
        onKeypress: event$ => event$.flatMap(evt => stream(emitter => {
            let pre = evt.target;
            let cmdOrCtrl = evt.metaKey || evt.ctrlKey;
            let code = evt.charCode;
            let ss = pre.selectionStart;
            let se = pre.selectionEnd;

            if (code && !cmdOrCtrl) {
                var character = String.fromCharCode(code);

                emitter.value(changeAction({
                    add: character,
                    del: ss === se ? '' : pre.textContent.slice(ss, se),
                    start: ss
                }));
            }

            emitter.end();
        })),
        onCut: event$ => event$.flatMap(evt => stream(emitter => {
            let pre = evt.target;
            let ss = pre.selectionStart;
            let se = pre.selectionEnd;
            let selection = ss === se ? '' : pre.textContent.slice(ss, se);

            if (selection) {
                emitter.value(changeAction({
                    add: '',
                    del: selection,
                    start: ss
                }));
            }
            emitter.end();
        })),
        onPaste: event$ => event$.flatMap(evt => stream(emitter => {
            let pre = evt.target;
            let ss = pre.selectionStart;
            let se = pre.selectionEnd;
            let selection = ss === se ? '' : pre.textContent.slice(ss, se);

            if (evt.clipboardData) {
                evt.preventDefault();

                let pasted = evt.clipboardData.getData('text/plain');

                document.execCommand('insertText', false, pasted);

                emitter.value(changeAction({
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

                    emitter.value(changeAction({
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
