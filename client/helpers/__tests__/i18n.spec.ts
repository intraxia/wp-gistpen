/* eslint-env jest */
import i18n from '../i18n';

describe('i18n', () => {
  beforeEach(() => {
    delete window.__GISTPEN_I18N__;
  });

  it('should sprintf the key', () => {
    const key = 'message';
    window.__GISTPEN_I18N__ = {
      [key]: 'A test message: %s %d'
    };

    expect(i18n(key, 'hello', 21)).toBe('A test message: hello 21');
  });

  it('should use the not found key if missing root key', () => {
    const key = 'message';
    window.__GISTPEN_I18N__ = {
      'i18n.notfound': 'Oh no!'
    };

    expect(i18n(key, 'hello', 21)).toBe('Oh no!');
  });

  it('should use default msg when missing root & fallback keys', () => {
    const key = 'message';

    expect(i18n(key, 'hello', 21)).toBe(
      'Translation & fallback not found for key message'
    );
  });
});
