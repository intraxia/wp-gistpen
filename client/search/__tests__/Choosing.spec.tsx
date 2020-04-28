import React from 'react';
import {
  fireEvent,
  RenderResult,
  waitFor as rtlWaitFor,
  act,
} from '@testing-library/react';
import { fakeServer, FakeServer } from 'nise';
import { defaultGlobals } from '../../reducers';
import { searchBlobSelected, searchRepoSelected } from '../actions';
import Choosing from '../Choosing';
import { GlobalsProvider } from '../context';
import { createSearchBlob, createSearchRepo } from '../../mocks';

const createInstance = ({
  container,
  getByText,
  getByTestId,
}: RenderResult) => {
  const elements = {
    input: () => getByTestId('search-input'),
    snippetList: () => getByTestId('snippet-list'),
    selectButton: () => getByText('Select'),
    errorNotice: () => container.querySelector('.components-notice.is-error'),
  };

  const fire = {
    inputChange: (value: string) =>
      fireEvent.change(elements.input(), { target: { value } }),
    selectButtonClick: () => fireEvent.click(elements.selectButton()),
  };

  const waitFor = {
    snippetList: () => rtlWaitFor(elements.snippetList),
  };

  return { elements, fire, waitFor };
};

const root = '/api/';

const element = (
  <GlobalsProvider value={{ ...defaultGlobals, root }}>
    <Choosing collection="blobs" />
  </GlobalsProvider>
);

const blob = createSearchBlob();
const repo = createSearchRepo();

describe('Choosing', () => {
  let server: FakeServer;

  beforeEach(() => {
    server?.restore();
    server = fakeServer.create();
    server.respondImmediately = true;
  });

  afterAll(() => {
    server?.restore();
  });

  it('should emit a blob on successful choice', () => {
    server.respondWith(
      'GET',
      `${root}search/blobs?s=js`,
      JSON.stringify([blob]),
    );

    expect(element).toEmitFromJunction(
      [[350, KTU.value(searchBlobSelected(blob))]],
      (rr, tick) => {
        const { fire } = createInstance(rr);

        fire.inputChange('js');

        act(() => {
          tick(350);
        });

        fire.selectButtonClick();
      },
    );
  });

  it('should emit a repo on successful choice', () => {
    server.respondWith(
      'GET',
      `${root}search/repos?s=js`,
      JSON.stringify([repo]),
    );

    expect(
      <GlobalsProvider value={{ ...defaultGlobals, root }}>
        <Choosing collection="repos" />
      </GlobalsProvider>,
    ).toEmitFromJunction(
      [[350, KTU.value(searchRepoSelected(repo))]],
      (rr, tick) => {
        const { fire } = createInstance(rr);

        fire.inputChange('js');

        act(() => {
          tick(350);
        });

        fire.selectButtonClick();
      },
    );
  });

  it('should show an error on failed API response', () => {
    server.respondWith('GET', `${root}search/blobs?s=js`, xhr => {
      xhr.respond(
        500,
        {},
        JSON.stringify({
          error: 'Something went wrong',
        }),
      );
    });

    expect(element).toEmitFromJunction([], (rr, tick) => {
      const { fire, elements } = createInstance(rr);

      fire.inputChange('js');

      act(() => {
        tick(350);
      });

      expect(elements.errorNotice()).toBeInTheDocument();
    });
  });
});
