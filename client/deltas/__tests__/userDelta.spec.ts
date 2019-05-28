/* eslint-env mocha */
import { expect, use } from 'chai';
import Kefir from 'kefir';
import { chaiPlugin } from 'brookjs-desalinate';
import sinon from 'sinon';
import { userDelta } from '../userDelta';

const { plugin } = chaiPlugin({ Kefir }) as any;

use(plugin);

describe('userDelta', () => {
  it('should ignore random actions', () => {
    const state = {};
    const action = {
      type: 'RANDOM'
    };
    expect(userDelta({ ajax$: sinon.stub() })).to.emitFromDelta([], send => {
      send(action, state);
    });
  });
});
