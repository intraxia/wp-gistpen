import React from 'react';
import { storiesOf } from '@storybook/react';
import Commits from '../';

storiesOf('Commits', module).add('default', () => (
  <Commits
    {...{
      prism: {
        theme: 'default',
        'line-numbers': false,
        'show-invisibles': false
      },
      selectedCommit: {
        description: 'Commit Description',
        states: [
          {
            ID: '1',
            code: 'console.log("test");',
            filename: 'test.js',
            language: 'javascript'
          },
          {
            ID: '2',
            code: 'console.log("test");',
            filename: 'test.js',
            language: 'javascript'
          }
        ]
      },
      commits: [
        {
          ID: '1',
          selected: true,
          description: 'Commit Description',
          committedAt: '1970-01-01',
          author: {
            name: 'Commit Author',
            avatar: 'http://via.placeholder.com/48x48'
          }
        },
        {
          ID: '2',
          selected: false,
          description: 'Commit Description',
          committedAt: '1970-01-01',
          author: {
            name: 'Commit Author',
            avatar: 'http://via.placeholder.com/48x48'
          }
        }
      ]
    }}
  />
));
