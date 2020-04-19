/* eslint-env jest */
import React from 'react';
import { fireEvent } from '@testing-library/react';
import { gistTokenChange } from '../../../../actions';
import Accounts from '../';

describe('Accounts', () => {
  it('should emit action when clicked', () => {
    expect(<Accounts token={''} />).toEmitFromJunction(
      [[0, Kutil.value(gistTokenChange('abc'))]],
      ({ queryByTestId }) => {
        const input = queryByTestId('gist-token') as Element;

        fireEvent.change(input, { target: { value: 'abc' } });
      },
    );
  });
});
