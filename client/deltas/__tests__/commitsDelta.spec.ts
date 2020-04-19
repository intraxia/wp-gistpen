/* eslint-env jest */
import sinon from 'sinon';
import { ObsResponse } from '../../ajax';
import {
  routeChange,
  commitsFetchStarted,
  commitsFetchFailed,
  commitsFetchSucceeded,
} from '../../actions';
import { commitsDelta } from '../commitsDelta';

const createServices = () => ({ ajax$: sinon.stub() });
const globals = {
  languages: {},
  root: '',
  nonce: 'asdf',
  url: '',
  ace_widths: [],
  statuses: {},
  themes: {},
};
const stateNoId = {
  globals,
  repo: {
    // no ID
    blobs: [],
    created_at: '',
    description: '',
    gist_id: '',
    html_url: '',
    password: '',
    commits_url: '',
    rest_url: '',
    status: '',
    sync: 'off',
    updated_at: '',
  },
};
const stateWithId = {
  globals,
  repo: {
    ID: 1234,
    blobs: [],
    created_at: '',
    description: '',
    gist_id: '',
    html_url: '',
    password: '',
    commits_url: 'http://testing.dev/api/commits/1234',
    rest_url: '',
    status: '',
    sync: 'off',
    updated_at: '',
  },
};

describe('commitsDelta', () => {
  it('should be a function', () => {
    expect(commitsDelta).toBeInstanceOf(Function);
  });

  it('should not respond to random actions', () => {
    const services = createServices();

    expect(commitsDelta(services)).toEmitFromDelta([], send => {
      send({ type: 'RANDOM_ACTION' }, stateNoId);
    });
  });

  it('should not respond to random routes', () => {
    const services = createServices();
    const actions$ = Kutil.stream();
    const state$ = Kutil.prop();

    expect(commitsDelta(services)).toEmitFromDelta([], () => {
      Kutil.send(state$, [Kutil.value(stateWithId)]);
      Kutil.send(actions$, [Kutil.value(routeChange('random'))]);
    });
  });

  it('should not respond to commits click for new repo', () => {
    const services = createServices();

    expect(commitsDelta(services)).toEmitFromDelta([], send => {
      send(routeChange('commits'), Kutil.value(stateNoId));
    });
  });

  it('should emit start and success on commits api success', () => {
    const commitsUrl = 'http://testing.dev/api/commits/1234';
    const options = {
      method: 'GET',
      credentials: 'include',
      headers: {
        'X-WP-Nonce': 'asdf',
        'Content-Type': 'application/json',
      },
    };
    const xhr = { response: JSON.stringify([]) } as any;
    const services = createServices();
    const effect$ = Kutil.stream();

    services.ajax$.withArgs(commitsUrl, options).onFirstCall().returns(effect$);

    expect(commitsDelta(services)).toEmitFromDelta(
      [
        [0, Kutil.value(commitsFetchStarted())],
        [10, Kutil.value(commitsFetchSucceeded([]))],
      ],
      (sendToDelta, tick) => {
        sendToDelta(routeChange('commits'), stateWithId);
        tick(10);
        Kutil.send(effect$, [Kutil.value(new ObsResponse(xhr)), Kutil.end()]);
      },
    );
  });

  it('should emit start and failure on commits api failure', () => {
    const commitsUrl = 'http://testing.dev/api/commits/1234';
    const options = {
      method: 'GET',
      credentials: 'include',
      headers: {
        'X-WP-Nonce': 'asdf',
        'Content-Type': 'application/json',
      },
    };
    const payload = new TypeError('Network request failed');
    const services = createServices();
    const effect$ = Kutil.stream();

    services.ajax$.withArgs(commitsUrl, options).onFirstCall().returns(effect$);

    expect(commitsDelta(services)).toEmitFromDelta(
      [
        [0, Kutil.value(commitsFetchStarted())],
        [10, Kutil.value(commitsFetchFailed(payload))],
      ],
      (sendToDelta, tick) => {
        sendToDelta(routeChange('commits'), stateWithId);
        tick(10);
        Kutil.send(effect$, [Kutil.error(payload), Kutil.end()]);
      },
    );
  });
});
