/* eslint-env jest */
import { expect, use } from 'chai';
import sinon from 'sinon';
import Kefir from 'kefir';
import { chaiPlugin } from 'brookjs-desalinate';
import { repoDelta } from '../repoDelta';

const { plugin } = chaiPlugin({ Kefir }) as any;

use(plugin);

const createServices = () => ({ ajax$: sinon.stub() });
const state = {
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
    commits_url: '',
    rest_url: '',
    status: '',
    sync: 'off',
    updated_at: ''
  },
  editor: {}
};

describe('repoDelta', () => {
  it('should be a function', () => {
    expect(repoDelta).to.be.a('function');
  });

  it('should not respond to random actions', () => {
    const services = createServices();

    expect(repoDelta(services)).to.emitFromDelta([], send => {
      send({ type: 'RANDOM_ACTION' }, state);
    });
  });
});
