/* eslint-env jest */
import { expect, use } from 'chai';
import Kefir from 'kefir';
import { chaiPlugin } from 'brookjs-desalinate';
import sinon from 'sinon';
import { siteDelta } from '../siteDelta';

const { plugin } = chaiPlugin({ Kefir }) as any;

use(plugin);

describe('siteDelta', () => {
  it('should ignore random actions', () => {
    const state = {};
    const action = {
      type: 'RANDOM'
    };
    expect(siteDelta({ ajax$: sinon.stub() })).to.emitFromDelta([], send => {
      send(action, state);
    });
  });
});
