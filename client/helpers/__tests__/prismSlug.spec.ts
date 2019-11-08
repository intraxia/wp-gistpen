/* eslint-env jest */
import prismSlug from '../prismSlug';

describe('prismSlug', () => {
  it('should use the alias', () => {
    expect(prismSlug('js')).toBe('javascript');
  });

  it('should return the slug if no alias found', () => {
    expect(prismSlug('hs')).toBe('hs');
  });
});
