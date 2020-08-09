import { storiesOf } from '@storybook/react';
import React from 'react';
import EditPage from '../';
import { JsonError } from '../../../api';

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
          cursor: null,
          language: 'js',
        },
      ]}
      languages={[]}
      errors={[new JsonError(new TypeError('API response was bad'))]}
    />
  </div>
));
