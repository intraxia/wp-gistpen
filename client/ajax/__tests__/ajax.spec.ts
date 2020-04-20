/* eslint-env jest */
import sinon, {
  SinonFakeXMLHttpRequestStatic,
  SinonFakeXMLHttpRequest,
} from 'sinon';
import { ajax$, ObsResponse } from '../';

describe('ajax$', () => {
  const url = 'http://example.com/api';
  let xhr: SinonFakeXMLHttpRequestStatic,
    requests: Array<SinonFakeXMLHttpRequest>;

  beforeEach(() => {
    requests = [];
    xhr = sinon.useFakeXMLHttpRequest();

    xhr.onCreate = xhr => {
      requests.push(xhr);
    };
  });

  afterEach(() => {
    xhr.restore();
  });

  it('should be a function', () => {
    expect(ajax$).toBeInstanceOf(Function);
  });

  it('should make an xhr request', () => {
    ajax$(url).observe();
    expect(requests).toHaveLength(1);
    const [request] = requests;
    expect(request.url).toBe(url);
  });

  it('should emit an error on failure', () => {
    expect(ajax$(url)).toEmit(
      [KTU.error(new TypeError('Network request failed')), KTU.end()],
      () => {
        requests[0].error();
      },
    );
  });

  it('should emit response on success', () => {
    const ajax = ajax$(url);
    const expected = [KTU.value({}), KTU.end()];

    expect(ajax).toEmit(expected, () => {
      const request = requests[0];
      expected[0] = KTU.value(new ObsResponse(request as any));
      request.respond(200, null, '');
    });
  });
});
