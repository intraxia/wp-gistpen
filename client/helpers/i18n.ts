import { sprintf } from 'sprintf-js';

export default function i18n(key: string, ...args: any[]): string {
  const { __GISTPEN_I18N__ = {} } = window;

  if (__GISTPEN_I18N__[key]) {
    return sprintf(__GISTPEN_I18N__[key], ...args);
  }

  return sprintf(
    __GISTPEN_I18N__['i18n.notfound'] ||
      'Translation & fallback not found for key %s',
    key
  );
}
