import { createStore, combineReducers } from 'redux';
import { fromESObservable } from 'kefir';
import root from './root';
import { applyDelta } from '../delta';
import { api, editor, repo } from '../reducer';

const { __GISTPEN_EDITOR__ } = global;

Object.defineProperty(HTMLPreElement.prototype, 'selectionStart', {
    get: function() {
        var selection = getSelection();

        if (selection.rangeCount) {
            let range = selection.getRangeAt(0);
            let element = range.startContainer;
            let container = element;
            let offset = range.startOffset;

            if (!(this.compareDocumentPosition(element) & 0x10)) {
                return 0;
            }

            do {
                while (element = element.previousSibling) {
                    if (element.textContent) {
                        offset += element.textContent.length;
                    }
                }

                element = container = container.parentNode;
            } while (element && element !== this);

            return offset;
        } else {
            return 0;
        }
    },

    enumerable: true,
    configurable: true
});

Object.defineProperty(HTMLPreElement.prototype, 'selectionEnd', {
    get: function() {
        var selection = getSelection();

        if (selection.rangeCount) {
            return this.selectionStart + (selection.getRangeAt(0) + '').length;
        } else {
            return 0;
        }
    },

    enumerable: true,
    configurable: true
});

HTMLPreElement.prototype.setSelectionRange = function(ss, se) {
    let range = document.createRange();
    let offset = findOffset(this, ss);

    range.setStart(offset.element, offset.offset);

    if (se && se !== ss) {
        offset = findOffset(this, se);
    }

    range.setEnd(offset.element, offset.offset);

    let selection = window.getSelection();
    selection.removeAllRanges();
    selection.addRange(range);
};

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

// eslint-disable-next-line camelcase
__webpack_public_path__ = __GISTPEN_EDITOR__.api.url + 'assets/js/';

const store = createStore(
    combineReducers({ api, editor, repo }),
    __GISTPEN_EDITOR__,
    applyDelta()
);
const state$ = fromESObservable(store).toProperty(store.getState);

document.addEventListener('DOMContentLoaded', () => {
    const app$ = root(document.querySelector('[data-brk-container="editor"]'), state$);

    if (process.env.NODE_ENV !== 'production') {
        app$.spy('app$');
    }

    app$.observe({ value: store.dispatch });
});
