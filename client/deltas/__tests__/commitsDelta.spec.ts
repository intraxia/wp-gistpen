/* eslint-env mocha */
import { ObsResponse } from '../../ajax';
import '../../polyfills';
import { expect, use } from 'chai';
import sinon from 'sinon';
import Kefir from 'kefir';
import { chaiPlugin } from 'brookjs-desalinate';
import {
  routeChange,
  commitsFetchStarted,
  commitsFetchFailed,
  commitsFetchSucceeded
} from '../../actions';
import { commitsDelta } from '../commitsDelta';
import { RootAction } from '../../util';

const { plugin, stream, prop, value, error, end, send } = chaiPlugin({ Kefir });

use(plugin);

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
    expect(commitsDelta).to.be.a('function');
  });

  it('should not respond to random actions', () => {
    const services = createServices();

    expect(commitsDelta(services)).to.emitFromDelta([], send => {
      send({ type: 'RANDOM_ACTION' }, stateNoId);
    });
  });

  it('should not respond to random routes', () => {
    const services = createServices();
    const actions$ = stream();
    const state$ = prop();

    expect(commitsDelta(services)).to.emitFromDelta([], () => {
      send(state$, [value(stateWithId)]);
      send(actions$, [value(routeChange('random'))]);
    });
  });

  it('should not respond to commits click for new repo', () => {
    const services = createServices();

    expect(commitsDelta(services)).to.emitFromDelta([], send => {
      send(routeChange('commits'), value(stateNoId));
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
    const effect$ = stream();

    services.ajax$
      .withArgs(commitsUrl, options)
      .onFirstCall()
      .returns(effect$);

    expect(commitsDelta(services)).to.emitFromDelta<RootAction, never>(
      [
        [0, value(commitsFetchStarted())],
        [10, value(commitsFetchSucceeded([]))]
      ],
      (sendToDelta, tick) => {
        sendToDelta(routeChange('commits'), stateWithId);
        tick(10);
        send(effect$, [value(new ObsResponse(xhr)), end()]);
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
    const effect$ = stream();

    services.ajax$
      .withArgs(commitsUrl, options)
      .onFirstCall()
      .returns(effect$);

    expect(commitsDelta(services)).to.emitFromDelta<RootAction, never>(
      [
        [0, value(commitsFetchStarted())],
        [10, value(commitsFetchFailed(payload))]
      ],
      (sendToDelta, tick) => {
        sendToDelta(routeChange('commits'), stateWithId);
        tick(10);
        send(effect$, [error(payload), end()]);
      }
    );
  });
});
