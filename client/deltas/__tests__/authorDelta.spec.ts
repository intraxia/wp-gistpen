/* eslint-env jest */
import sinon, { SinonStub } from 'sinon';
import Kefir from 'kefir';
import { authorDelta } from '../authorDelta';
import { AjaxService } from '../../ajax';
import {
  commitsFetchSucceeded,
  fetchAuthorFailed,
  fetchAuthorSucceeded,
} from '../../actions';

describe('authorDelta', () => {
  const state = {
    commits: {
      instances: [
        {
          author: '1',
        },
      ],
    },
    globals: {
      nonce: '12345',
    },
  };

  let services: { ajax$: AjaxService }, stub: SinonStub;

  beforeEach(() => {
    services = {} as any;
    services.ajax$ = stub = sinon.stub();
  });

  it('should be a function', () => {
    expect(authorDelta).toBeInstanceOf(Function);
  });

  it('should return a function', () => {
    expect(authorDelta(services)).toBeInstanceOf(Function);
  });

  it('should not emit anything on random action', () => {
    expect(authorDelta(services)).toEmitFromDelta([], send => {
      send({ type: 'ANYTHING' }, state);
    });
  });

  it('should emit error if request fails', () => {
    const error = new TypeError('Network error');

    stub.returns(Kefir.constantError(error));

    expect(authorDelta(services)).toEmitFromDelta(
      [[0, KTU.value(fetchAuthorFailed(error))]],
      send => {
        send(commitsFetchSucceeded({} as any), state);
      },
    );
  });

  it('should emit an error if json is parsed incorrectly', () => {
    const error = new TypeError('Error parsing JSON');

    stub.returns(
      Kefir.constant({
        json: () => Kefir.constantError(error),
      }),
    );

    expect(authorDelta(services)).toEmitFromDelta(
      [[0, KTU.value(fetchAuthorFailed(error))]],
      send => {
        send(commitsFetchSucceeded({} as any), state);
      },
    );
  });

  it('should emit an error if response does not match expected', () => {
    const error = new TypeError('Author response was not the expected shape');

    stub.returns(
      Kefir.constant({
        json: () =>
          Kefir.constant({
            random: 'property',
          }),
      }),
    );

    expect(authorDelta(services)).toEmitFromDelta(
      [[0, KTU.value(fetchAuthorFailed(error))]],
      send => {
        send(commitsFetchSucceeded({} as any), state);
      },
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
      avatar_urls: {},
    };

    stub.returns(
      Kefir.constant({
        json: () => Kefir.constant(response),
      }),
    );

    expect(authorDelta(services)).toEmitFromDelta(
      [[0, KTU.value(fetchAuthorSucceeded(response))]],
      send => {
        send(commitsFetchSucceeded({} as any), state);
      },
    );
  });
});
