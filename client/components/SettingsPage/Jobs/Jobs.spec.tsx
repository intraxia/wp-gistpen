/* eslint-env jest */
import React from 'react';
import { expect, use } from 'chai';
import Kefir from 'kefir';
import { chaiPlugin } from 'brookjs-desalinate';
import { fireEvent } from '@testing-library/react';
import { jobDispatchClick } from '../../../actions';
import Jobs from './';

const { value, plugin } = chaiPlugin({ Kefir });
use(plugin);

describe('Jobs', () => {
  it('should emit action when clicked', () => {
    expect(
      <Jobs
        jobs={[
          {
            name: 'Export',
            slug: 'export',
            description: 'Export things',
            status: 'idle' as const
          }
        ]}
      />
    ).to.emitFromJunction(
      [[350, value(jobDispatchClick('export'))]],
      ({ getByTestId }, tick) => {
        const button = getByTestId('dispatch-job-export') as Element;

        fireEvent.click(button);
        tick(100);
        fireEvent.click(button);
        tick(50);
        fireEvent.click(button);
      }
    );
  });
});
