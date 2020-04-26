import React from 'react';
import { RenderResult, fireEvent } from '@testing-library/react';
import { createNewClick, chooseExistingClick } from '../../actions';
import CreateOrChoose from '../CreateOrChoose';

describe('CreateOrChoose', () => {
  it('should emit a create new click', () => {
    expect(<CreateOrChoose />).toEmitFromJunction(
      [[0, KTU.value(createNewClick())]],
      ({ getByText }: RenderResult) => {
        fireEvent.click(getByText('Create new'));
      },
    );
  });

  it('should emit a choose existing click', () => {
    expect(<CreateOrChoose />).toEmitFromJunction(
      [[0, KTU.value(chooseExistingClick())]],
      ({ getByText }: RenderResult) => {
        fireEvent.click(getByText('Choose from existing'));
      },
    );
  });
});
