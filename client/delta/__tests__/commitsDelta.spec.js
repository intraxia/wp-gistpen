// @flow
/* eslint-env mocha */
import type { Action, HasRepo, HasGlobalsState } from '../../type';
import type { AjaxOptions } from '../../service';
import '../../polyfills';
import chai, { expect } from 'chai';
import sinonChai from 'sinon-chai';
import sinon from 'sinon';
import { Kefir } from 'brookjs';
import { routeChangeAction } from '../../action';

import commitsDelta from '../commitsDelta';

chai.use(sinonChai);

const createServices = () => ({ ajax$: sinon.stub() });

describe('commitsDelta', () => {
    it('should be a function', () => {
        expect(commitsDelta).to.be.a('function')
            .and.have.lengthOf(3);
    });

    it('should be curried', () => {
        expect(commitsDelta({})).to.be.a('function');
    });

    it('should return an Observable', () => {
        expect(commitsDelta(createServices(), Kefir.never(), Kefir.never())).to.be.an.instanceOf(Kefir.Observable);
    });

    it('should not respond to random actions', (done : () => void) => {
        const services = createServices();
        const actions$ = Kefir.later(10, { type: 'RANDOM_ACTION' });
        const state$: Kefir.Observable<HasRepo & HasGlobalsState> = Kefir.constant({
            globals: {
                languages: {},
                root: '',
                nonce: 'asdf',
                url: '',
                ace_widths: [],
                statuses: {},
                themes: {}
            },
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
        });

        const value = sinon.spy();
        const error = sinon.spy();

        commitsDelta(services, actions$, state$).observe({
            value,
            error,
            end() {
                expect(value).to.have.callCount(0);
                expect(error).to.have.callCount(0);
                done();
            }
        });
    });

    it('should not respond to random routes', (done : () => void) => {
        const services = createServices();
        const actions$: Kefir.Observable<Action> = Kefir.later(10, routeChangeAction('random'));
        const state$: Kefir.Observable<HasRepo & HasGlobalsState> = Kefir.constant({
            globals: {
                languages: {},
                root: '',
                nonce: 'asdf',
                url: '',
                ace_widths: [],
                statuses: {},
                themes: {}
            },
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
        });

        const value = sinon.spy();
        const error = sinon.spy();

        commitsDelta(services, actions$, state$).observe({
            value,
            error,
            end() {
                expect(error).to.have.callCount(0);
                expect(value).to.have.callCount(0);

                done();
            }
        });
    });

    it('should not respond to commits click for new repo', (done : () => void) => {
        const services = createServices();
        const actions$: Kefir.Observable<Action> = Kefir.later(10, routeChangeAction('commits'));
        const state$: Kefir.Observable<HasRepo & HasGlobalsState> = Kefir.constant({
            globals: {
                languages: {},
                root: '',
                nonce: 'asdf',
                url: '',
                ace_widths: [],
                statuses: {},
                themes: {}
            },
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
        });

        let calls = 0;

        commitsDelta(services, actions$, state$).observe({
            value() {
                calls++;
            },
            end() {
                expect(calls).to.equal(0);
                done();
            }
        });
    });

    it('should emit start and success on commits api success', (done : () => void) => {
        const options: AjaxOptions = {
            method: 'GET',
            credentials: 'include',
            headers: {
                'X-WP-Nonce': 'asdf',
                'Content-Type': 'application/json'
            }
        };
        const services = createServices();
        const actions$: Kefir.Observable<Action> = Kefir.later(10, routeChangeAction('commits'));
        const commitsUrl = 'http://testing.dev/api/commits/1234';
        const state$: Kefir.Observable<HasRepo & HasGlobalsState> = Kefir.constant({
            globals: {
                languages: {},
                root: '',
                nonce: 'asdf',
                url: '',
                ace_widths: [],
                statuses: {},
                themes: {}
            },
            repo: {
                ID: 1234,
                blobs: [],
                created_at: '',
                description: '',
                gist_id: '',
                html_url: '',
                password: '',
                commits_url: commitsUrl,
                rest_url: '',
                status: '',
                sync: 'off',
                updated_at: ''
            }
        });

        let calls = 0;
        services.ajax$
            .withArgs(commitsUrl, options)
            .onFirstCall()
            .returns(Kefir.later(10, JSON.stringify([])));

        commitsDelta(services, actions$, state$).observe({
            value(val : Action) {
                calls++;

                if (calls === 1) {
                    expect(val).to.eql({
                        type: 'COMMITS_FETCH_STARTED'
                    });
                }

                if (calls === 2) {
                    expect(val).to.eql({
                        type: 'COMMITS_FETCH_SUCCEEDED',
                        payload: {
                            response: []
                        }
                    });
                }
            },
            end() {
                expect(calls).to.equal(2);

                done();
            }
        });
    });

    it('should emit start and failure on commits api failure', (done : () => void) => {
        const options: AjaxOptions = {
            method: 'GET',
            credentials: 'include',
            headers: {
                'X-WP-Nonce': 'asdf',
                'Content-Type': 'application/json'
            }
        };
        const services = createServices();
        const actions$: Kefir.Observable<Action> = Kefir.later(10, routeChangeAction('commits'));
        const commitsUrl = 'http://testing.dev/api/commits/1234';
        const state$: Kefir.Observable<HasRepo & HasGlobalsState> = Kefir.constant({
            globals: {
                languages: {},
                root: '',
                nonce: 'asdf',
                url: '',
                ace_widths: [],
                statuses: {},
                themes: {}
            },
            repo: {
                ID: 1234,
                blobs: [],
                created_at: '',
                description: '',
                gist_id: '',
                html_url: '',
                password: '',
                commits_url: commitsUrl,
                rest_url: '',
                status: '',
                sync: 'off',
                updated_at: ''
            }
        });

        let calls = 0;
        const payload = new TypeError('Network request failed');
        services.ajax$
            .withArgs(commitsUrl, options)
            .onFirstCall()
            .returns(
                Kefir.constantError(payload)
                    .delay(10)
            );

        commitsDelta(services, actions$, state$).observe({
            value(val : Action) {
                calls++;

                if (calls === 1) {
                    expect(val).to.eql({
                        type: 'COMMITS_FETCH_STARTED'
                    });
                }

                if (calls === 2) {
                    expect(val).to.eql({
                        type: 'COMMITS_FETCH_FAILED',
                        payload,
                        error: true
                    });
                }
            },
            end() {
                expect(calls).to.equal(2);

                done();
            }
        });
    });
});
