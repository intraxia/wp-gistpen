import { storiesOf } from '@storybook/react';
import React from 'react';
import { AjaxError } from '../../../ajax';
import EditPage from '../';

storiesOf('EditPage', module).add('with error', () => (
  <div id="wpbody">
    <EditPage
      description=""
      loading={false}
      invisibles={'off'}
      statuses={[]}
      themes={[]}
      widths={[]}
      selectedTheme="twilight"
      selectedStatus=""
      selectedWidth="2"
      gist={{ show: false }}
      sync="off"
      tabs="off"
      instances={[
        {
          ID: '1',
          code: '\n',
          filename: '',
          cursor: false as const,
          language: 'js',
        },
      ]}
      languages={[]}
      errors={[
        new AjaxError(
          'API response was bad',
          400,
          JSON.stringify({ message: 'A more specific BE message' }),
        ),
      ]}
    />
  </div>
));
