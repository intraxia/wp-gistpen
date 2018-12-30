/* eslint-env mocha */
import { expect, use } from 'chai';
import sinon, { SinonStub } from 'sinon';
import Kefir from 'kefir';
import { chaiPlugin } from 'brookjs-desalinate';
import { authorDelta } from '../authorDelta';
import { AjaxService } from '../../ajax';
import {
  commitsFetchSucceeded,
  fetchAuthorFailed,
  fetchAuthorSucceeded
} from '../../actions';

const { plugin, value } = chaiPlugin({ Kefir });
use(plugin);

describe('authorDelta', () => {
  const state = {
    commits: {
      instances: [
        {
          author: '1'
        }
      ]
    },
    globals: {
      nonce: '12345'
    }
  };

  let services: { ajax$: AjaxService }, stub: SinonStub;

  beforeEach(() => {
    services = {} as any;
    services.ajax$ = stub = sinon.stub();
  });

  it('should be a function', () => {
    expect(authorDelta).to.be.a('function');
  });

  it('should return a function', () => {
    expect(authorDelta(services)).to.be.a('function');
  });

  it('should not emit anything on random action', () => {
    expect(authorDelta(services)).to.emitFromDelta([], send => {
      send({ type: 'ANYTHING' }, state);
    });
  });

  it('should emit error if request fails', () => {
    const error = new TypeError('Network error');

    stub.returns(Kefir.constantError(error));

    expect(authorDelta(services)).to.emitFromDelta(
      [[0, value(fetchAuthorFailed(error))]],
      send => {
        send(commitsFetchSucceeded({}), state);
      }
    );
  });

  it('should emit an error if json is parsed incorrectly', () => {
    const error = new TypeError('Error parsing JSON');

    stub.returns(
      Kefir.constant({
        json: () => Kefir.constantError(error)
      })
    );

    expect(authorDelta(services)).to.emitFromDelta(
      [[0, value(fetchAuthorFailed(error))]],
      send => {
        send(commitsFetchSucceeded({}), state);
      }
    );
  });

  it.skip('should emit an error if response does not match expected', () => {
    const error = new TypeError('Author response was not the expected shape');

    stub.returns(
      Kefir.constant({
        json: () =>
          Kefir.constant({
            random: 'property'
          })
      })
    );

    expect(authorDelta(services)).to.emitFromDelta(
      [[0, value(fetchAuthorFailed(error))]],
      send => {
        send(commitsFetchSucceeded({}), state);
      }
    );
  });

  it('should emit success', () => {
    const response = {
      id: 1,
      name: 'Hello',
      url: 'https://hello.com/',
      description: 'World!',
      link: 'https://world.com/',
      slug: 'hello-world',
      avatar_urls: {}
    };

    stub.returns(
      Kefir.constant({
        json: () => Kefir.constant(response)
      })
    );

    expect(authorDelta(services)).to.emitFromDelta(
      [[0, value(fetchAuthorSucceeded(response))]],
      send => {
        send(commitsFetchSucceeded({}), state);
      }
    );
  });
});
