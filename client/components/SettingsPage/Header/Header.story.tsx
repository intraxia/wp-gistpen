import { storiesOf } from '@storybook/react';
import React from 'react';
import Header from './';

storiesOf('Header', module)
  .add('default', () => (
    <Header {...{ route: 'highlighting', loading: false }} />
  ))
  .add('loading', () => (
    <Header {...{ route: 'highlighting', loading: true }} />
  ));
