import { storiesOf } from '@storybook/react';
import React from 'react';
import Loader from './';

storiesOf('Loader', module).add('default', () => (
  <Loader text={'Loading...'} />
));
