import { storiesOf } from '@storybook/react';
import React from 'react';
import Instance from './index';

storiesOf('Editor', module).add('default', () => (
  <div id="wpbody">
    <Instance
      {...{
        code: "console.log('hello')",
        filename: 'storybook.js',
        cursor: false,
        theme: 'twilight',
        invisibles: 'off',
        languages: [
          {
            label: 'JavaScript',
            value: 'js'
          },
          {
            label: 'Python',
            value: 'python'
          }
        ],
        language: 'js'
      }}
    />
  </div>
));
