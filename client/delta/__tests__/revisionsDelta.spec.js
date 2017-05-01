// @flow
/* eslint-env mocha */
import type { Action, AjaxOptions, HasRepo, HasApiConfig } from '../../type';
import Kefir from 'kefir';
import chai, { expect } from 'chai';
import sinonChai from 'sinon-chai';
import sinon from 'sinon';
import { editorRevisionsClick } from '../../action';

import revisionsDelta from '../revisionsDelta';

chai.use(sinonChai);

const createServices = () => ({ ajax$: sinon.stub() });

describe('revisionsDelta', () => {
    it('should be a function', () => {
        expect(revisionsDelta).to.be.a('function')
            .and.have.lengthOf(3);
    });

    it('should be curried', () => {
        expect(revisionsDelta({})).to.be.a('function');
    });

    it('should return an Observable', () => {
        expect(revisionsDelta(createServices(), Kefir.never(), Kefir.never())).to.be.an.instanceOf(Kefir.Observable);
    });

    it('should not respond to random actions', (done : () => void) => {
        const services = createServices();
        const actions$ = Kefir.later(10, { type: 'RANDOM_ACTION' });
        const state$ : Kefir.Observable<HasRepo & HasApiConfig> = Kefir.constant({
            api: {
                nonce: 'asdf',
                root: '',
                url: ''
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

        revisionsDelta(services, actions$, state$).observe({
            value() {
                calls++;
            },
            end() {
                expect(calls).to.equal(0);
                done();
            }
        });
    });

    it('should not respond to revisions click for new repo', (done : () => void) => {
        const services = createServices();
        const actions$ : Kefir.Observable<Action> = Kefir.later(10, editorRevisionsClick());
        const state$ : Kefir.Observable<HasRepo & HasApiConfig> = Kefir.constant({
            api: {
                nonce: 'asdf',
                root: '',
                url: ''
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

        revisionsDelta(services, actions$, state$).observe({
            value() {
                calls++;
            },
            end() {
                expect(calls).to.equal(0);
                done();
            }
        });
    });

    it('should emit start and success on revisions api success',(done : () => void) => {
        const options : AjaxOptions = {
            method: 'GET',
            credentials: 'include',
            headers: {
                'X-WP-Nonce': 'asdf',
                'Content-Type': 'application/json'
            }
        };
        const services = createServices();
        const actions$ : Kefir.Observable<Action> = Kefir.later(10, editorRevisionsClick());
        const commitsUrl = 'http://testing.dev/api/commits/1234';
        const state$ : Kefir.Observable<HasRepo & HasApiConfig> = Kefir.constant({
            api: {
                nonce: 'asdf',
                root: '',
                url: ''
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

        revisionsDelta(services, actions$, state$).observe({
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

    it('should emit start and failure on revisions api failure',(done : () => void) => {
        const options : AjaxOptions = {
            method: 'GET',
            credentials: 'include',
            headers: {
                'X-WP-Nonce': 'asdf',
                'Content-Type': 'application/json'
            }
        };
        const services = createServices();
        const actions$ : Kefir.Observable<Action> = Kefir.later(10, editorRevisionsClick());
        const commitsUrl = 'http://testing.dev/api/commits/1234';
        const state$ : Kefir.Observable<HasRepo & HasApiConfig> = Kefir.constant({
            api: {
                nonce: 'asdf',
                root: '',
                url: ''
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
            .returns(
                Kefir.constantError(new TypeError('Network request failed'))
                    .delay(10)
            );

        revisionsDelta(services, actions$, state$).observe({
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
                        payload: new TypeError('Network request failed'),
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
