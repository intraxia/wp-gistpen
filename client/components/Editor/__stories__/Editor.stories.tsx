import { storiesOf } from '@storybook/react';
import React from 'react';
import Editor from '../';

const props: React.ComponentProps<typeof Editor> = {
  code: "console.log('hello')",
  filename: 'storybook.js',
  cursor: null,
  theme: 'twilight',
  invisibles: 'off',
  embedCode: '[gistpen id="2"]',
  languages: [
    {
      label: 'JavaScript',
      value: 'js',
    },
    {
      label: 'Python',
      value: 'python',
    },
  ],
  language: 'js',
};

storiesOf('Editor', module)
  .add('default', () => (
    <div id="wpbody">
      <Editor {...props} />
    </div>
  ))
  .add('no filename or content', () => (
    <div id="wpbody">
      <Editor
        {...{
          ...props,
          ...{ code: '', filename: '' },
        }}
      />
    </div>
  ));
