// @flow
/* eslint-env mocha */
import type { AjaxOptions } from '../../services';
import { ObsResponse } from '../../services';
import '../../polyfills';
import { expect, use } from 'chai';
import sinonChai from 'sinon-chai';
import sinon from 'sinon';
import { Kefir } from 'brookjs';
import { chaiPlugin } from 'brookjs-desalinate';
import { routeChangeAction } from '../../actions';
import commitsDelta from '../commitsDelta';

const { plugin, stream, prop, value, error, end, send } = chaiPlugin({ Kefir });

use(sinonChai);
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
        expect(commitsDelta).to.be.a('function')
            .and.have.lengthOf(3);
    });

    it('should be curried', () => {
        expect(commitsDelta({})).to.be.a('function');
    });

    it('should return an Observable', () => {
        expect(commitsDelta(createServices(), Kefir.never(), Kefir.never())).to.be.an.observable();
    });

    it('should not respond to random actions', () => {
        const services = createServices();
        const action$ = stream();
        const state$ = prop();

        expect(commitsDelta(services, action$, state$)).to.emit([], () => {
            send(state$,  [value(stateNoId)]);
            send(action$, [value({ type: 'RANDOM_ACTION' })]);
        });
    });

    it('should not respond to random routes', () => {
        const services = createServices();
        const actions$ = stream();
        const state$ = prop();

        expect(commitsDelta(services, actions$, state$)).to.emit([], () => {
            send(state$, [value(stateWithId)]);
            send(actions$, [value(routeChangeAction('random'))]);
        });
    });

    it('should not respond to commits click for new repo', () => {
        const services = createServices();
        const actions$ = stream(); // Kefir.later(10, );
        const state$ = prop();

        expect(commitsDelta(services, actions$, state$)).to.emit([], () => {
            send(state$, [value(stateNoId)]);
            send(actions$, [value(routeChangeAction('commits'))]);
        });
    });

    it('should emit start and success on commits api success', () => {
        const commitsUrl = 'http://testing.dev/api/commits/1234';
        const options: AjaxOptions = {
            method: 'GET',
            credentials: 'include',
            headers: {
                'X-WP-Nonce': 'asdf',
                'Content-Type': 'application/json'
            }
        };
        const xhr = (({ response: JSON.stringify([]) }: any): XMLHttpRequest);
        const services = createServices();
        const actions$ = stream();
        const state$ = prop();
        const effect$ = stream();

        services.ajax$
            .withArgs(commitsUrl, options)
            .onFirstCall()
            .returns(effect$);

        expect(commitsDelta(services, actions$, state$)).to.emitInTime([
            [0, value({
                type: 'COMMITS_FETCH_STARTED'
            })],
            [10, value({
                type: 'COMMITS_FETCH_SUCCEEDED',
                payload: {
                    response: []
                }
            })]
        ], tick => {
            send(state$, [value(stateWithId)]);
            send(actions$, [value(routeChangeAction('commits'))]);
            tick(10);
            send(effect$, [value(new ObsResponse(xhr)), end()]);
        });
    });

    it('should emit start and failure on commits api failure', () => {
        const commitsUrl = 'http://testing.dev/api/commits/1234';
        const options: AjaxOptions = {
            method: 'GET',
            credentials: 'include',
            headers: {
                'X-WP-Nonce': 'asdf',
                'Content-Type': 'application/json'
            }
        };
        const payload = new TypeError('Network request failed');
        const services = createServices();
        const actions$ = stream();
        const state$ = prop();
        const effect$ = stream();

        services.ajax$
            .withArgs(commitsUrl, options)
            .onFirstCall()
            .returns(effect$);

        expect(commitsDelta(services, actions$, state$)).to.emitInTime([
            [0, value({
                type: 'COMMITS_FETCH_STARTED'
            })],
            [10, value({
                type: 'COMMITS_FETCH_FAILED',
                payload,
                error: true
            })]
        ], tick => {
            send(state$, [value(stateWithId)]);
            send(actions$, [value(routeChangeAction('commits'))]);
            tick(10);
            send(effect$, [error(payload), end()]);
        });
    });
});
