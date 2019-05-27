type OSSuccess = { element: Node; offset: number; error?: null };
type OSError = { element: null; offset: 0; error: true };
type Offset = OSSuccess | OSError;

export default function findOffset(root: Element, ss: number): Offset {
  let container;
  let offset = 0;
  let element: ChildNode | null = root;

  do {
    container = element;
    element = element.firstChild;

    if (element) {
      do {
        const len = (element.textContent || '').length;

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
        offset: (element.textContent || '').length
      };
    } else {
      return {
        element: element,
        offset: 0
      };
    }
  }

  return {
    element: null,
    offset: 0,
    error: true
  };
}
