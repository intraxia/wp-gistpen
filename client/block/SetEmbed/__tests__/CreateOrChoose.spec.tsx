import { RenderResult, fireEvent } from '@testing-library/react';
import { createNewClick, chooseExistingClick } from '../../actions';
import { basic } from '../__stories__/CreateOrChoose.stories';

describe('CreateOrChoose', () => {
  it('should emit a create new click', () => {
    expect(basic()).toEmitFromJunction(
      [[0, KTU.value(createNewClick())]],
      ({ getByText }: RenderResult) => {
        fireEvent.click(getByText('Create'));
      },
    );
  });

  it('should emit a choose existing click', () => {
    expect(basic()).toEmitFromJunction(
      [[0, KTU.value(chooseExistingClick())]],
      ({ getByText }: RenderResult) => {
        fireEvent.click(getByText('Choose'));
      },
    );
  });
});
