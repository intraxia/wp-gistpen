import { storiesOf } from '@storybook/react';
import React from 'react';
import Description from '../Description';

storiesOf('Description', module)
  .add('without text - not loading', () => (
    <Description
      {...{
        description: '',
        loading: false
      }}
    />
  ))
  .add('with text - not loading', () => (
    <Description
      {...{
        description: 'New Repo',
        loading: false
      }}
    />
  ))
  .add('with text - loading', () => (
    <Description
      {...{
        description: 'New Repo',
        loading: true
      }}
    />
  ));
