import './index.scss';
import R from 'ramda';
import { fromEvents, fromPromise, merge, never, stream } from 'kefir';
import { editorCursorMoveAction, editorIndentAction, editorMakeCommentAction,
    editorMakeNewlineAction, editorRedoAction, editorUndoAction,
    editorValueChangeAction } from '../../action';
import Prism from '../../prism';
import component from 'brookjs/component';
import render from 'brookjs/render';
import events from 'brookjs/events';
import { selectSelectionStart, selectSelectionEnd } from '../../selector';
import template from './index.hbs';

// const CRLF = /\r?\n|\r/g;

const updateLinenumber = (pre, start, end) => stream(emitter => {
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
    const { textContent: code } = evt.target;
    const ss = selectSelectionStart(evt.target);
    const se = selectSelectionEnd(evt.target);
    const cursor = [ss, se];

    switch (evt.keyCode) {
        case 9: // Tab
            if (!cmdOrCtrl) {
                return editorIndentAction({ code, cursor, inverse });
            }
            break;
        case 13:
            return editorMakeNewlineAction({ code, cursor });
        case 90:
            if (cmdOrCtrl) {
                return inverse ? editorRedoAction() : editorUndoAction();
            }
            break;
        case 191:
            if (cmdOrCtrl && !altKey) {
                return editorMakeCommentAction({ code, cursor });
            }
            break;
    }

    return false;
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

const mapToTargetCursorAction = R.map(R.pipe(
    R.prop('target'),
    R.converge(
        R.unapply(editorCursorMoveAction),
        [selectSelectionStart, selectSelectionEnd]
    )
));

export default component({
    onMount: (el, props$) => stream(emitter => {
        Prism.setAutoloaderPath(__webpack_public_path__);

        emitter.end();
    })
        .merge(props$.sampledBy(fromEvents(el, 'keyup').debounce(150))
            .merge(props$.take(1))
            .flatMapLatest(state => fromPromise(Promise.all([
                Prism.setTheme(state.editor.theme),
                Prism.togglePlugin('show-invisibles', state.editor.invisibles === 'on')
            ]))
                .ignoreValues()
                .concat(merge([
                    renderTemplate(el, state, state),
                    highlightElement(el),
                    state.instance.cursor ? setSelectionRange(el.querySelector('code'), ...state.instance.cursor) : never(),
                    state.instance.cursor ? updateLinenumber(el.querySelector('pre'), ...state.instance.cursor) : never()
                ])))
    ),
    events: events({
        onBlur: R.map(R.always(editorCursorMoveAction(false))),
        onClick: mapToTargetCursorAction,
        onFocus: mapToTargetCursorAction,
        onKeydown: R.pipe(
            R.map(mapKeydownToAction),
            R.filter(R.identity)
        ),
        onInput: R.map(({ target }) =>
            editorValueChangeAction({
                code: target.textContent,
                cursor: [selectSelectionStart(target), selectSelectionEnd(target)]
            })
        )
    })
});
