import React from 'react';
import { storiesOf } from '@storybook/react';
import SearchPopup from '../';

storiesOf('SearchPopup', module)
  .add('without results; not loading', () => (
    <SearchPopup
      {...{
        loading: false,
        term: '',
        results: [],
      }}
    />
  ))
  .add('with results; not loading', () => (
    <SearchPopup
      {...{
        loading: false,
        term: 'test',
        results: [
          {
            id: '1',
            filename: 'test1.js',
          },
          {
            id: '2',
            filename: 'test2.js',
          },
        ],
      }}
    />
  ))
  .add('without results; loading', () => (
    <SearchPopup
      {...{
        loading: true,
        term: '',
        results: [],
      }}
    />
  ))
  .add('with results; loading', () => (
    <SearchPopup
      {...{
        loading: true,
        term: 'test',
        results: [
          {
            id: '1',
            filename: 'test1.js',
          },
          {
            id: '2',
            filename: 'test2.js',
          },
        ],
      }}
    />
  ));
