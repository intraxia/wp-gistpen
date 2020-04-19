/* eslint-env jest */
import sinon from 'sinon';
import { repoDelta } from '../repoDelta';

const createServices = () => ({ ajax$: sinon.stub() });
const state = {
  globals: {
    languages: {},
    root: '',
    nonce: 'asdf',
    url: '',
    ace_widths: [],
    statuses: {},
    themes: {},
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
    updated_at: '',
  },
  editor: {},
};

describe('repoDelta', () => {
  it('should be a function', () => {
    expect(repoDelta).toBeInstanceOf(Function);
  });

  it('should not respond to random actions', () => {
    const services = createServices();

    expect(repoDelta(services)).toEmitFromDelta([], send => {
      send({ type: 'RANDOM_ACTION' }, state);
    });
  });
});
