/* eslint-env mocha */
import { expect } from 'chai';
import prismSlug from '../prismSlug';

describe('prismSlug', () => {
  it('should use the alias', () => {
    expect(prismSlug('js')).to.equal('javascript');
  });

  it('should return the slug if no alias found', () => {
    expect(prismSlug('hs')).to.equal('hs');
  });
});
