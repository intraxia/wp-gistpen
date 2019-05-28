export function selectSelectionStart(node: Element): number {
  const selection = getSelection();

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
