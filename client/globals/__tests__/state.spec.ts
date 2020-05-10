/* eslint-env jest */
import { init } from '../../actions';
import { globalsReducer } from '../state';

describe('globalsReducer', () => {
  it('should return state on random action', () => {
    const state = {
      languages: {},
      root: '',
      nonce: '',
      url: '',
      ace_widths: [],
      statuses: {},
      themes: {},
      demo: {
        filename: '',
        code: '',
        language: '',
      },
    };
    const action = {
      type: 'RANDOM',
    } as any;
    expect(globalsReducer(state, action)).toBe(state);
  });

  it('should merge globals from init action', () => {
    const state = {
      languages: {},
      root: '',
      nonce: '',
      url: '',
      ace_widths: [],
      statuses: {},
      themes: {},
      demo: {
        filename: '',
        code: '',
        language: '',
      },
    };
    const action = init({
      globals: {
        root: 'the-root',
      },
    });

    expect(globalsReducer(state, action)).toEqual({
      languages: {},
      root: 'the-root',
      nonce: '',
      url: '',
      ace_widths: [],
      statuses: {},
      themes: {},
      demo: {
        filename: '',
        code: '',
        language: '',
      },
    });
  });
});
