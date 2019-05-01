import { storiesOf } from '@storybook/react';
import React from 'react';
import Runs from './';

storiesOf('Runs', module).add('default', () => (
  <Runs
    {...{
      name: 'Export',
      status: 'idle',
      runs: [
        {
          ID: '1',
          status: 'finished',
          scheduled_at: 'Yesterday',
          started_at: 'Today',
          finished_at: 'Tomorrow',
          job: 'export',
          rest_url: '',
          job_url: '',
          console_url: ''
        }
      ]
    }}
  />
));
