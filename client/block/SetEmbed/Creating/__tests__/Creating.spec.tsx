import React from 'react';
import { RenderResult, fireEvent, act } from '@testing-library/react';
import { FakeServer, fakeServer } from 'nise';
import Creating from '../';
import {
  createApiRepo,
  createApiBlob,
  createSearchRepo,
} from '../../../../mocks';
import { newBlobAttached, newRepoCreated } from '../../../actions';
import { GlobalsProvider, defaultGlobals } from '../../../../globals';

const root = '/api/';

const element = (
  <GlobalsProvider value={{ ...defaultGlobals, root }}>
    <Creating />
  </GlobalsProvider>
);

const createInstance = (rr: RenderResult) => {
  const elements = {
    chooseButton: () => rr.getByText('Add to existing repo'),
    createButton: () => rr.getByText('Create new repo'),
    searchInput: () => rr.getByLabelText('Search repos'),
    selectButton: () => rr.getByText('Select'),
    descriptionInput: () => rr.getByLabelText('Gistpen description'),
    filenameInput: () => rr.getByLabelText('Snippet filename'),
    saveButton: () => rr.getByText('Create repo'),
  };

  const fire = {
    chooseClick: () => fireEvent.click(elements.chooseButton()),
    createClick: () => fireEvent.click(elements.createButton()),
    searchInputChange: (value: string) =>
      fireEvent.change(elements.searchInput(), { target: { value } }),
    descriptionInputChange: (value: string) =>
      fireEvent.change(elements.descriptionInput(), { target: { value } }),
    filenameInputChange: (value: string) =>
      fireEvent.change(elements.filenameInput(), { target: { value } }),
    selectClick: () => fireEvent.click(elements.selectButton()),
    saveClick: () => fireEvent.click(elements.saveButton()),
  };

  const waitFor = {};

  return { elements, fire, waitFor };
};

describe('Creating', () => {
  let server: FakeServer;

  beforeEach(() => {
    server?.restore();
    server = fakeServer.create();
  });

  afterAll(() => {
    server.restore();
  });

  it('should choose an existing repo and create & set blob on it', () => {
    const repo = createSearchRepo();
    const blob = createApiBlob();
    server.respondWith(
      'GET',
      `${root}search/repos?s=Test%20Repo`,
      // Existing repo with no blobs
      JSON.stringify([repo]),
    );

    server.respondWith('POST', `${root}repos/${repo.ID}/blobs`, [
      201,
      {},
      JSON.stringify(blob),
    ]);

    expect(element).toEmitFromJunction(
      [[350, KTU.value(newBlobAttached(blob.ID, repo.ID))]],
      (rr, tick) => {
        const { elements, fire } = createInstance(rr);

        fire.chooseClick();
        fire.searchInputChange('Test Repo');
        fire.filenameInputChange('filename.js');

        act(() => {
          tick(350);
          server.respond();
        });

        fire.selectClick();

        expect(elements.selectButton()).toBeDisabled();
        expect(server.lastRequest?.requestBody).toEqual(
          JSON.stringify({
            filename: 'filename.js',
          }),
        );

        act(() => {
          server.respond();
        });
      },
    );
  });

  it('should create a new repo with a blob on it', () => {
    const repo = createApiRepo({
      blobs: [createApiBlob()],
    });
    server.respondWith('POST', `${root}repos`, [201, {}, JSON.stringify(repo)]);

    expect(element).toEmitFromJunction(
      [[0, KTU.value(newRepoCreated(repo))]],
      rr => {
        const { elements, fire } = createInstance(rr);

        fire.createClick();
        fire.descriptionInputChange('Test Repo');
        fire.filenameInputChange('filename.js');
        fire.saveClick();

        expect(elements.saveButton()).toBeDisabled();
        expect(server.lastRequest?.requestBody).toEqual(
          JSON.stringify({
            description: 'Test Repo',
            blobs: [
              {
                filename: 'filename.js',
              },
            ],
          }),
        );

        act(() => {
          server.respond();
        });
      },
    );
  });
});
