import { storiesOf } from '@storybook/react';
import React from 'react';
import Messages from './';

storiesOf('Messages', module).add('default', () => (
  <Messages
    {...{
      job: 'Export',
      job_id: '2',
      status: 'finished',
      messages: [
        {
          ID: '1',
          run_id: '2',
          text: 'This is a message',
          level: 'info',
          logged_at: 'yesterday'
        }
      ]
    }}
  />
));
