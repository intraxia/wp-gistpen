/* eslint-env jest */
import { ObsResponse } from '../../ajax';
import sinon from 'sinon';
import {
  routeChange,
  commitsFetchStarted,
  commitsFetchFailed,
  commitsFetchSucceeded
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
  themes: {}
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
    updated_at: ''
  }
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
    updated_at: ''
  }
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
    const actions$ = global.Kutil.stream();
    const state$ = global.Kutil.prop();

    expect(commitsDelta(services)).toEmitFromDelta([], () => {
      global.Kutil.send(state$, [global.Kutil.value(stateWithId)]);
      global.Kutil.send(actions$, [global.Kutil.value(routeChange('random'))]);
    });
  });

  it('should not respond to commits click for new repo', () => {
    const services = createServices();

    expect(commitsDelta(services)).toEmitFromDelta([], send => {
      send(routeChange('commits'), global.Kutil.value(stateNoId));
    });
  });

  it('should emit start and success on commits api success', () => {
    const commitsUrl = 'http://testing.dev/api/commits/1234';
    const options = {
      method: 'GET',
      credentials: 'include',
      headers: {
        'X-WP-Nonce': 'asdf',
        'Content-Type': 'application/json'
      }
    };
    const xhr = { response: JSON.stringify([]) } as any;
    const services = createServices();
    const effect$ = global.Kutil.stream();

    services.ajax$
      .withArgs(commitsUrl, options)
      .onFirstCall()
      .returns(effect$);

    expect(commitsDelta(services)).toEmitFromDelta(
      [
        [0, global.Kutil.value(commitsFetchStarted())],
        [10, global.Kutil.value(commitsFetchSucceeded([]))]
      ],
      (sendToDelta, tick) => {
        sendToDelta(routeChange('commits'), stateWithId);
        tick(10);
        global.Kutil.send(effect$, [
          global.Kutil.value(new ObsResponse(xhr)),
          global.Kutil.end()
        ]);
      }
    );
  });

  it('should emit start and failure on commits api failure', () => {
    const commitsUrl = 'http://testing.dev/api/commits/1234';
    const options = {
      method: 'GET',
      credentials: 'include',
      headers: {
        'X-WP-Nonce': 'asdf',
        'Content-Type': 'application/json'
      }
    };
    const payload = new TypeError('Network request failed');
    const services = createServices();
    const effect$ = global.Kutil.stream();

    services.ajax$
      .withArgs(commitsUrl, options)
      .onFirstCall()
      .returns(effect$);

    expect(commitsDelta(services)).toEmitFromDelta(
      [
        [0, global.Kutil.value(commitsFetchStarted())],
        [10, global.Kutil.value(commitsFetchFailed(payload))]
      ],
      (sendToDelta, tick) => {
        sendToDelta(routeChange('commits'), stateWithId);
        tick(10);
        global.Kutil.send(effect$, [
          global.Kutil.error(payload),
          global.Kutil.end()
        ]);
      }
    );
  });
});
