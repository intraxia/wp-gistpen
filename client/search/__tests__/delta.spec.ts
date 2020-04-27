/* eslint-env jest */
import { fakeServer, FakeServer } from 'nise';
import { searchDelta } from '../delta';
import { search } from '../actions';
import { AjaxError } from '../../ajax';
import { searchBlobsApiResponse } from '../../mocks';

const state = {
  root: '/api/',
  nonce: 'abcd',
  term: 'js',
  collection: 'blobs',
};

describe('delta', () => {
  describe('searchDelta', () => {
    let server: FakeServer;

    beforeEach(() => {
      server?.restore();
      server = fakeServer.create();
    });

    it('should ignore random actions', () => {
      const action = {
        type: 'RANDOM',
      };
      expect(searchDelta).toEmitFromDelta([], send => {
        send(action, state);
      });
    });

    it('should ignore request with empty term', () => {
      expect(searchDelta).toEmitFromDelta([], send => {
        send(search.request(), { ...state, term: '' });
      });
    });

    it('should debounce 300ms before making request', () => {
      expect(searchDelta).toEmitFromDelta([], (send, tick) => {
        send(search.request(), state);
        tick(299);

        expect(server.requests).toHaveLength(0);
      });
    });

    it('should cancel in flight request', () => {
      expect(searchDelta).toEmitFromDelta([], (send, tick) => {
        send(search.request(), state);
        tick(350);

        expect(server.requests).toHaveLength(1);

        send(search.cancel(), state);

        expect(server.lastRequest?.status).toBe(0);
      });
    });

    it('should emit error event on bad JSON', () => {
      expect(searchDelta).toEmitFromDelta(
        [
          [
            350,
            KTU.value(
              search.failure(
                new TypeError(
                  'Error parsing JSON response: Unexpected end of JSON input',
                ),
              ),
            ),
          ],
        ],
        (send, tick) => {
          send(search.request(), state);
          tick(350);

          server.lastRequest?.respond(200, {}, '{"broken"');
        },
      );
    });

    it('should emit error event on bad response format', () => {
      const msg = `Search API response validation failed:\n\n* Invalid value {} supplied to 1/response`;
      expect(searchDelta).toEmitFromDelta(
        [[350, KTU.value(search.failure(new AjaxError(msg)))]],
        (send, tick) => {
          send(search.request(), state);
          tick(350);

          server.lastRequest?.respond(200, {}, JSON.stringify({}));
        },
      );
    });

    it('should emit success', () => {
      expect(searchDelta).toEmitFromDelta(
        [
          [
            350,
            KTU.value(
              search.success({
                collection: 'blobs',
                response: searchBlobsApiResponse,
              }),
            ),
          ],
        ],
        (send, tick) => {
          send(search.request(), state);
          tick(350);

          server.lastRequest?.respond(
            200,
            {},
            JSON.stringify(searchBlobsApiResponse),
          );
        },
      );
    });
  });
});
