import { storiesOf } from '@storybook/react';
import React from 'react';
import Highlighting from './';

storiesOf('Highlighting', module).add('default', () => (
  <Highlighting
    {...{
      demo: {
        code: `console.log('test');`,
        filename: 'test.js',
        language: 'javascript'
      },
      theme: {
        options: [
          {
            name: 'Default',
            slug: 'default'
          },
          {
            name: 'Twilight',
            slug: 'twilight'
          }
        ],
        selected: 'default'
      },
      'line-numbers': true,
      'show-invisibles': true
    }}
  />
));
