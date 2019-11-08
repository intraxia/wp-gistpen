/* eslint-env jest */
import sinon from 'sinon';
import { searchDelta } from '../searchDelta';

describe('searchDelta', () => {
  it('should ignore random actions', () => {
    const state = {};
    const action = {
      type: 'RANDOM'
    };
    expect(searchDelta({ ajax$: sinon.stub() })).toEmitFromDelta([], send => {
      send(action, state);
    });
  });
});
