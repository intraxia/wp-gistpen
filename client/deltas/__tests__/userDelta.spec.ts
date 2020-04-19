/* eslint-env jest */
import sinon from 'sinon';
import { userDelta } from '../userDelta';

describe('userDelta', () => {
  it('should ignore random actions', () => {
    const state = {};
    const action = {
      type: 'RANDOM',
    };
    expect(userDelta({ ajax$: sinon.stub() })).toEmitFromDelta([], send => {
      send(action, state);
    });
  });
});
