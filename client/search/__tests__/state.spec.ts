import { loop } from 'brookjs';
import { defaultGlobals } from '../../globals';
import { reducer, Initial, Found, Searching, Researching } from '../state';
import {
  searchInput,
  search,
  searchResultSelectionChange,
  searchBlobSelected,
} from '../actions';
import { searchBlobsApiResponse } from '../../mocks';
import { AjaxError } from '../../ajax';

describe('state', () => {
  describe('reducer', () => {
    it('should return default state on random action', () => {
      const state: Initial = {
        status: 'initial',
        term: '',
        collection: 'blobs',
        globals: defaultGlobals,
      };

      expect(reducer(state, { type: 'RANDOM' } as any)).toEqual(state);
    });

    it('should return the new term with a search request', () => {
      const state: Initial = {
        status: 'initial',
        term: '',
        collection: 'blobs',
        globals: defaultGlobals,
      };

      expect(reducer(state, searchInput('js'))).toEqual(
        loop(
          {
            status: 'initial',
            term: 'js',
            collection: 'blobs',
            globals: defaultGlobals,
          },
          search.request(),
        ),
      );
    });

    it('should return the new term with a cancel request', () => {
      const state: Initial = {
        status: 'initial',
        term: 'js',
        collection: 'blobs',
        globals: defaultGlobals,
      };

      expect(reducer(state, searchInput(''))).toEqual(
        loop(
          {
            status: 'initial',
            term: '',
            collection: 'blobs',
            globals: defaultGlobals,
          },
          search.cancel(),
        ),
      );
    });

    it('should return state with selected snippet on selection change', () => {
      const state: Found = {
        status: 'found',
        term: 'js',
        collection: 'blobs',
        globals: defaultGlobals,
        results: { collection: 'blobs', response: searchBlobsApiResponse },
      };

      const selection = searchBlobsApiResponse[2];

      expect(reducer(state, searchResultSelectionChange(selection.ID))).toEqual(
        loop(state, searchBlobSelected(selection)),
      );
    });

    it('should return state when no snippets present', () => {
      const state: Initial = {
        status: 'initial',
        term: 'js',
        collection: 'blobs',
        globals: defaultGlobals,
      };

      const selection = searchBlobsApiResponse[2];

      expect(reducer(state, searchResultSelectionChange(selection.ID))).toEqual(
        state,
      );
    });

    it('should transition to searching from initial on request', () => {
      const state: Initial = {
        status: 'initial',
        term: 'js',
        collection: 'blobs',
        globals: defaultGlobals,
      };

      expect(reducer(state, search.request())).toEqual({
        status: 'searching',
        term: 'js',
        collection: 'blobs',
        globals: defaultGlobals,
      });
    });

    it('should transition to researching from found on request', () => {
      const state: Found = {
        status: 'found',
        term: 'js',
        collection: 'blobs',
        globals: defaultGlobals,
        results: { collection: 'blobs', response: searchBlobsApiResponse },
      };

      expect(reducer(state, search.request())).toEqual({
        status: 'researching',
        term: 'js',
        collection: 'blobs',
        globals: defaultGlobals,
        results: { collection: 'blobs', response: searchBlobsApiResponse },
      });
    });

    it('should ignore request on searching', () => {
      const state: Searching = {
        status: 'searching',
        term: 'js',
        collection: 'blobs',
        globals: defaultGlobals,
      };

      expect(reducer(state, search.request())).toEqual({
        status: 'searching',
        term: 'js',
        collection: 'blobs',
        globals: defaultGlobals,
      });
    });

    it('should transition to found on api success', () => {
      const state: Searching = {
        status: 'searching',
        term: 'js',
        collection: 'blobs',
        globals: defaultGlobals,
      };

      expect(
        reducer(
          state,
          search.success({
            collection: 'blobs',
            response: searchBlobsApiResponse,
          }),
        ),
      ).toEqual({
        status: 'found',
        term: 'js',
        collection: 'blobs',
        results: { collection: 'blobs', response: searchBlobsApiResponse },
        globals: defaultGlobals,
      });
    });

    it('should transition to error when searching on failure', () => {
      const state: Searching = {
        status: 'searching',
        term: 'js',
        collection: 'blobs',
        globals: defaultGlobals,
      };
      const msg = '500 - Internal Server Error';

      expect(reducer(state, search.failure(new AjaxError(msg)))).toEqual({
        status: 'error',
        term: 'js',
        collection: 'blobs',
        error: msg,
        globals: defaultGlobals,
      });
    });

    it('should transition to error when searching on failure', () => {
      const state: Researching = {
        status: 'researching',
        term: 'js',
        collection: 'blobs',
        results: { collection: 'blobs', response: searchBlobsApiResponse },
        globals: defaultGlobals,
      };
      const msg = '500 - Internal Server Error';

      expect(reducer(state, search.failure(new AjaxError(msg)))).toEqual({
        status: 'reerror',
        term: 'js',
        collection: 'blobs',
        error: msg,
        results: { collection: 'blobs', response: searchBlobsApiResponse },
        globals: defaultGlobals,
      });
    });

    it('should ignore transition on other statuses', () => {
      const state: Initial = {
        status: 'initial',
        term: 'js',
        collection: 'blobs',
        globals: defaultGlobals,
      };
      const msg = '500 - Internal Server Error';

      expect(reducer(state, search.failure(new AjaxError(msg)))).toEqual(state);
    });
  });
});
