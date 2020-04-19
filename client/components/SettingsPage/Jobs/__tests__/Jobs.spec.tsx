/* eslint-env jest */
import React from 'react';
import { fireEvent } from '@testing-library/react';
import { jobDispatchClick } from '../../../../actions';
import Jobs from '../';

describe('Jobs', () => {
  it('should emit action when clicked', () => {
    expect(
      <Jobs
        jobs={[
          {
            name: 'Export',
            slug: 'export',
            description: 'Export things',
            status: 'idle' as const,
          },
        ]}
      />,
    ).toEmitFromJunction(
      [[350, KTU.value(jobDispatchClick('export'))]],
      ({ getByTestId }, tick) => {
        const button = getByTestId('dispatch-job-export') as Element;

        fireEvent.click(button);
        tick(100);
        fireEvent.click(button);
        tick(50);
        fireEvent.click(button);
      },
    );
  });
});
