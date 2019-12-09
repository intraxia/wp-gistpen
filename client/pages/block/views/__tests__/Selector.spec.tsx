/* eslint-env jest */
import { render, fireEvent, RenderResult, wait } from '@testing-library/react';
import { createRender } from 'react-testing-kit';
import Kefir from 'kefir';
import Selector from '../Selector';
import { ajax$, ObsResponse } from '../../../../ajax';
import { ApiRepo } from '../../../../api';

jest.mock('../../../../ajax', () => ({
  ajax$: jest.fn(),
  ObsResponse: class ObsResponse {
    json = jest.fn();
  }
}));

const baseRepo: ApiRepo = {
  ID: 123,
  description: 'New Repo',
  status: 'draft',
  password: '',
  gist_id: '',
  gist_url: null,
  rest_url: '',
  html_url: '',
  commits_url: '',
  created_at: '',
  updated_at: '',
  blobs: [
    {
      ID: 456,
      filename: '',
      code: '',
      size: 0,
      raw_url: '',
      edit_url: '',
      language: {
        ID: 789,
        display_name: 'PlainText',
        slug: 'plaintext'
      }
    }
  ],
  sync: 'off'
};

const renderSelector = createRender({
  defaultProps: { setIds: jest.fn() },
  component: Selector,
  render,
  elements: ({
    getByText,
    getByTestId,
    getByLabelText,
    getAllByTestId
  }: RenderResult) => ({
    noSnippetSelectedHeader: () => getByText(/no snippet/i),
    addToWhatHeader: () => getByText(/add to what?/i),
    addToNewHeader: () => getByText(/add to new/i),
    addToExistingHeader: () => getByText(/add to existing/i),
    addButton: () => getByTestId('block-add-btn'),
    addToNewButton: () => getByTestId('block-add-to-new-btn'),
    addToExistingButton: () => getByTestId('block-add-to-existing-btn'),
    backButton: () => getByText(/back/i),
    newDescription: () => getByLabelText(/description/i),
    saveNewButton: () => getByTestId('block-save-new-btn'),
    saveExistingButton: () => getByTestId('block-save-existing-btn'),
    search: () => getByLabelText(/search/i),
    searchResult: (idx: number) =>
      getAllByTestId('block-add-to-existing-search-result')[idx]
  }),
  fire: elements => ({
    addButtonClick: () => fireEvent.click(elements.addButton()),
    addToNewButtonClick: () => fireEvent.click(elements.addToNewButton()),
    addToExistingButtonClick: () =>
      fireEvent.click(elements.addToExistingButton()),
    backButtonClick: () => fireEvent.click(elements.backButton()),
    newDescriptionChange: (value: string) =>
      fireEvent.change(elements.newDescription(), { target: { value } }),
    saveExistingButtonClick: () =>
      fireEvent.click(elements.saveExistingButton()),
    saveNewButtonClick: () => fireEvent.click(elements.saveNewButton()),
    searchChange: (value: string) =>
      fireEvent.change(elements.search(), { target: { value } }),
    searchResultClick: (idx: number) =>
      fireEvent.click(elements.searchResult(idx))
  }),
  waitFor: () => ({})
});

describe('Selector', () => {
  beforeEach(() => {
    (ajax$ as jest.Mock).mockReset();
  });

  it('should render without crashing', () => {
    const { container } = renderSelector();

    expect(container).toBeInTheDocument();
  });

  it('should navigate in and out of add flows', () => {
    const { fire, elements } = renderSelector();

    fire.addButtonClick();

    expect(elements.addToWhatHeader()).toBeInTheDocument();

    fire.addToNewButtonClick();

    expect(elements.addToNewHeader()).toBeInTheDocument();

    fire.backButtonClick();

    expect(elements.addToWhatHeader()).toBeInTheDocument();

    fire.addToExistingButtonClick();

    expect(elements.addToExistingHeader()).toBeInTheDocument();

    fire.backButtonClick();
    fire.backButtonClick();

    expect(elements.noSnippetSelectedHeader()).toBeInTheDocument();
  });

  it('should create new repo with empty blob and set as existing', () => {
    const response = new ObsResponse({} as any);
    (response.json as jest.Mock).mockReturnValue(Kefir.constant(baseRepo));
    (ajax$ as jest.Mock).mockReturnValue(Kefir.constant(response));

    const { fire, props } = renderSelector();

    fire.addButtonClick();
    fire.addToNewButtonClick();
    fire.newDescriptionChange(baseRepo.description);
    fire.saveNewButtonClick();

    expect(ajax$).toHaveBeenCalledTimes(1);
    expect(ajax$).toHaveBeenCalledWith(
      'http://localhost/wp-json/intraxia/v1/gistpen/repos',
      {
        method: 'POST',
        credentials: 'include',
        headers: {
          'X-WP-Nonce': window.__GISTPEN_TINYMCE__.globals.nonce,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          description: baseRepo.description,
          blobs: [{ filename: 'draft', code: '' }]
        })
      }
    );
    expect(props.setIds).toHaveBeenCalledTimes(1);
    expect(props.setIds).toHaveBeenCalledWith(
      baseRepo.ID,
      baseRepo.blobs[0].ID
    );
  });

  it('should search for and add to existing repo', async () => {
    const response = new ObsResponse({} as any);
    (response.json as jest.Mock).mockReturnValue(Kefir.constant([baseRepo]));
    (ajax$ as jest.Mock).mockReturnValue(Kefir.constant(response));

    const { fire, props } = renderSelector();

    fire.addButtonClick();
    fire.addToExistingButtonClick();
    fire.searchChange(baseRepo.description);

    // is debounced
    await wait(() => expect(ajax$).toHaveBeenCalledTimes(1));

    fire.searchResultClick(0);
    fire.saveExistingButtonClick();

    expect(ajax$).toHaveBeenCalledWith(
      `http://localhost/wp-json/intraxia/v1/gistpen/search?s=${encodeURIComponent(
        baseRepo.description
      )}`,
      {
        method: 'POST',
        credentials: 'include',
        headers: {
          'X-WP-Nonce': window.__GISTPEN_TINYMCE__.globals.nonce,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          description: baseRepo.description,
          blobs: [{ filename: 'draft', code: '' }]
        })
      }
    );
    expect(props.setIds).toHaveBeenCalledTimes(1);
    expect(props.setIds).toHaveBeenCalledWith(
      baseRepo.ID,
      baseRepo.blobs[0].ID
    );
  });
});
