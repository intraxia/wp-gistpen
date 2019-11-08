/* eslint-env jest */
import sinon from 'sinon';
import { siteDelta } from '../siteDelta';

describe('siteDelta', () => {
  it('should ignore random actions', () => {
    const state = {};
    const action = {
      type: 'RANDOM'
    };
    expect(siteDelta({ ajax$: sinon.stub() })).toEmitFromDelta([], send => {
      send(action, state);
    });
  });
});
