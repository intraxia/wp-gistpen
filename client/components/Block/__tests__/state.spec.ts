import { reducer } from '../state';

describe('state', () => {
  describe('reducer', () => {
    it('should return the same state on random actions', () => {
      const initialState = {
        status: 'set-embed',
        repoId: null,
        blobId: null,
      } as const;
      expect(reducer(initialState, { type: '@@RANDOM' } as any)).toEqual(
        initialState,
      );
    });
  });
});
