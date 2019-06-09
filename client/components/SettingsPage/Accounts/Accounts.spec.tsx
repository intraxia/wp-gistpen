/* eslint-env mocha */
import React from 'react';
import { expect, use } from 'chai';
import Kefir from 'kefir';
import { chaiPlugin } from 'brookjs-desalinate';
import { fireEvent } from '@testing-library/react';
import { gistTokenChange } from '../../../actions';
import Accounts from './';

const { value, plugin } = chaiPlugin({ Kefir });
use(plugin);

describe('Accounts', () => {
  it.skip('should emit action when clicked', () => {
    expect(<Accounts token={''} />).to.emitFromJunction(
      [[0, value(gistTokenChange('abc'))]],
      ({ queryByTestId }) => {
        const input = queryByTestId('token-input') as Element;

        // @TODO(James) doesn't fire correctly
        fireEvent.change(input, { target: { value: 'abc' } });
      }
    );
  });
});
