/* eslint-env mocha */
import { expect, use } from 'chai';
import Kefir from 'kefir';
import { chaiPlugin } from 'brookjs-desalinate';
import { webpackDelta } from '../webpackDelta';

const { plugin, end } = chaiPlugin({ Kefir }) as any;

use(plugin);

describe('webpackDelta', () => {
  it('should set public path on initial state', () => {
    (global as any).__webpack_public_path__ = null;
    const state = {
      globals: {
        url: 'https://test.dev/'
      }
    };
    const action = {
      type: 'INIT'
    };
    expect(webpackDelta).to.emitFromDelta([[0, end()]], send => {
      send(action, state);
    });

    expect((global as any).__webpack_public_path__).to.equal(
      'https://test.dev/assets/js/'
    );
  });
});
