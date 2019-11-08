/* eslint-env jest */
import { expect, use } from 'chai';
import sinon, {
  SinonFakeXMLHttpRequestStatic,
  SinonFakeXMLHttpRequest
} from 'sinon';
import Kefir from 'kefir';
import { chaiPlugin } from 'brookjs-desalinate';
import { ajax$, ObsResponse } from '../';

const { plugin, value, error, end } = chaiPlugin({ Kefir });
use(plugin);

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
    expect(ajax$).to.be.a('function');
  });

  it('should make an xhr request', () => {
    ajax$(url).observe();
    expect(requests).to.have.lengthOf(1);
    const [request] = requests;
    expect(request.url).to.equal(url);
  });

  it.skip('should emit an error on failure', () => {
    expect(ajax$(url)).to.emit(
      [
        // @todo doesn't pass on current version of deep-eql
        error(new TypeError('Network request failed')),
        end()
      ],
      () => {
        requests[0].error();
      }
    );
  });

  it('should emit response on success', () => {
    const ajax = ajax$(url);
    const expected = [value({}), end()];

    expect(ajax).to.emit(expected, () => {
      const request = requests[0];
      expected[0] = value(new ObsResponse(request as any));
      request.respond(200, null, '');
    });
  });
});
