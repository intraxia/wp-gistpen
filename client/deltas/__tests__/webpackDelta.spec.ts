/* eslint-env jest */
import { webpackDelta } from '../webpackDelta';

describe('webpackDelta', () => {
  it('should set public path on initial state', () => {
    const state = {
      globals: {
        url: 'https://test.dev/'
      }
    };
    const action = {
      type: 'INIT'
    };
    expect(webpackDelta).toEmitFromDelta([[0, Kutil.end()]], send => {
      send(action, state);
    });

    expect(window.__webpack_public_path__).toBe('https://test.dev/assets/js/');
  });
});
