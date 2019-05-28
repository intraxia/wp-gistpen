/* eslint-env mocha */
import { expect, use } from 'chai';
import Kefir from 'kefir';
import { chaiPlugin } from 'brookjs-desalinate';
import sinon from 'sinon';
import { searchDelta } from '../searchDelta';

const { plugin } = chaiPlugin({ Kefir }) as any;

use(plugin);

describe('searchDelta', () => {
  it('should ignore random actions', () => {
    const state = {};
    const action = {
      type: 'RANDOM'
    };
    expect(searchDelta({ ajax$: sinon.stub() })).to.emitFromDelta([], send => {
      send(action, state);
    });
  });
});
