import { storiesOf } from '@storybook/react';
import React from 'react';
import Jobs from '../';

storiesOf('Jobs', module).add('default', () => (
  <Jobs
    {...{
      jobs: [
        {
          name: 'Export',
          slug: 'export',
          description: 'Export things',
          status: 'idle',
        },
      ],
    }}
  />
));
