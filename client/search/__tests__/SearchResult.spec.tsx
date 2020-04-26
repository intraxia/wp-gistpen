import React from 'react';
import { fireEvent, act } from '@testing-library/react';
import { blob, prism } from '../../mocks';
import SearchResult from '../SearchResult';
import { searchResultSelectClick } from '../actions';

const element = (
  <SearchResult
    filename={blob.filename}
    render={{
      id: 1,
      filename: blob.filename,
      blob,
      prism,
    }}
  />
);

describe('SearchResult', () => {
  it('should emit select click', () => {
    expect(element).toEmitFromJunction(
      [[0, KTU.value(searchResultSelectClick())]],
      ({ getByText }) => {
        fireEvent.click(getByText('Select'));
      },
    );
  });

  it('should open and close popover', () => {
    expect(element).toEmitFromJunction(
      [],
      ({ getByText, getByTestId }, tick) => {
        fireEvent.click(getByText('View'));

        const popover = getByTestId('search-result-popover');

        expect(popover).toBeInTheDocument();

        act(() => {
          tick(160);
        });

        fireEvent.blur(popover);

        act(() => {
          tick(160);
        });

        expect(popover).not.toBeInTheDocument();
      },
    );
  });
});
