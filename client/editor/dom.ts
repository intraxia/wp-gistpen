export function selectSelectionStart(node: Element): number {
  const selection = window.getSelection();

  if (selection && selection.rangeCount) {
    const range = selection.getRangeAt(0);
    let element: Node | null = range.startContainer;
    let container: Node | null = element;
    let offset = range.startOffset;

    if (!container || !(node.compareDocumentPosition(element) & 0x10)) {
      return 0;
    }

    do {
      while ((element = element.previousSibling)) {
        if (element.textContent) {
          offset += element.textContent.length;
        }
      }

      element = container = container.parentNode;
    } while (container && element && element !== node);

    return offset;
  } else {
    return 0;
  }
}

export function selectSelectionEnd(node: Element): number {
  const selection = getSelection();

  if (selection && selection.rangeCount) {
    return (
      selectSelectionStart(node) + selection.getRangeAt(0).toString().length
    );
  } else {
    return 0;
  }
}

export function lineNumberIsEqual(/* prev, next */): boolean {
  // @todo implement with line numbers.
  return true;
}

export function languageIsEqual(
  prev: { language: string },
  next: { language: string },
): boolean {
  return prev.language === next.language;
}

export function isSpecialEvent(
  evt: KeyboardEvent | React.KeyboardEvent,
): boolean {
  const { altKey, metaKey, ctrlKey } = evt;
  const cmdOrCtrl = metaKey || ctrlKey;

  switch (evt.keyCode) {
    case 9: // Tab
      if (!cmdOrCtrl && !altKey) {
        return true;
      }
      break;
    case 13:
      return true;
    case 90:
      if (cmdOrCtrl) {
        return true;
      }
      break;
    case 191:
      if (cmdOrCtrl && !altKey) {
        return true;
      }
      break;
  }

  return false;
}

type OSSuccess = { element: Node; offset: number; error?: null };
type OSError = { element: null; offset: 0; error: true };
type Offset = OSSuccess | OSError;

export function findOffset(root: Element, ss: number): Offset {
  // @TODO(mAAhaTTah) these types seem sketchy...
  let container;
  let offset = 0;
  let element: ChildNode | null = root;

  do {
    container = element;
    element = element.firstChild;

    if (element) {
      do {
        const len = (element.textContent ?? '').length;

        if (offset <= ss && offset + len > ss) {
          break;
        }

        offset += len;
      } while ((element = element.nextSibling));
    }

    if (!element) {
      // It's the container's lastChild
      break;
    }
  } while (element?.hasChildNodes() && element.nodeType !== 3);

  if (element) {
    return {
      element: element,
      offset: ss - offset,
    };
  } else if (container) {
    element = container;

    while (element && element.lastChild) {
      element = element.lastChild;
    }

    if (element.nodeType === 3) {
      return {
        element: element,
        offset: (element.textContent || '').length,
      };
    } else {
      return {
        element: element,
        offset: 0,
      };
    }
  }

  return {
    element: null,
    offset: 0,
    error: true,
  };
}
