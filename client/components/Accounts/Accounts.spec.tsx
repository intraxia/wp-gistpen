/* eslint-env mocha */
import React from 'react';
import { expect, use } from 'chai';
import Kefir from 'kefir';
import { chaiPlugin } from 'brookjs-desalinate';
import { fireEvent } from 'react-testing-library';
import { gistTokenChange } from '../../actions';
import Accounts from './';

const { value, plugin } = chaiPlugin({ Kefir });
use(plugin);

describe('Accounts', () => {
  it('should emit action when clicked', () => {
    expect(<Accounts token={''} />).to.emitFromJunction(
      [[0, value(gistTokenChange('abc'))]],
      ({ queryByTestId }) => {
        const input = queryByTestId('token-input') as Element;

        fireEvent.input(input, { target: { value: 'abc' } });
      }
    );
  });
});
