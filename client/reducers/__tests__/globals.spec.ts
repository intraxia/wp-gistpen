/* eslint-env mocha */
import { globalsReducer } from '../globals';
import { expect } from 'chai';
import { init } from '../../actions';

describe('globalsReducer', () => {
  it('should return state on random action', () => {
    const state = {
      languages: {},
      root: '',
      nonce: '',
      url: '',
      ace_widths: [],
      statuses: {},
      themes: {}
    };
    const action = {
      type: 'RANDOM'
    } as any;
    expect(globalsReducer(state, action)).to.equal(state);
  });

  it('should merge globals from init action', () => {
    const state = {
      languages: {},
      root: '',
      nonce: '',
      url: '',
      ace_widths: [],
      statuses: {},
      themes: {}
    };
    const action = init({
      globals: {
        root: 'the-root'
      }
    });

    expect(globalsReducer(state, action)).to.deep.equal({
      languages: {},
      root: 'the-root',
      nonce: '',
      url: '',
      ace_widths: [],
      statuses: {},
      themes: {}
    });
  });
});
