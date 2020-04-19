import { Props } from './types';

export function editorOptionsIsEqual(
  prev: Pick<Props, 'theme' | 'invisibles'>,
  next: Pick<Props, 'theme' | 'invisibles'>,
): boolean {
  return prev.theme === next.theme && prev.invisibles === next.invisibles;
}

export function lineNumberIsEqual(/* prev, next */): boolean {
  // @todo implement with line numbers.
  return true;
}

export function languageIsEqual(
  prev: Pick<Props, 'language'>,
  next: Pick<Props, 'language'>,
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
