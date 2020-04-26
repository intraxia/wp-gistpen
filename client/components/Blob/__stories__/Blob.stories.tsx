import { storiesOf } from '@storybook/react';
import React from 'react';
import Blob from '../';
import { props, prism, blob } from '../../../mocks';

storiesOf('Blob', module)
  .add('default', () => <Blob {...props} />)
  .add('with line-numbers', () => (
    <Blob
      {...{
        blob,
        prism: { ...prism, 'line-numbers': true },
      }}
    />
  ))
  .add('with invisibles', () => (
    <Blob
      {...{
        blob,
        prism: { ...prism, 'show-invisibles': true },
      }}
    />
  ))
  .add('with inivisibles and line-numbers', () => (
    <Blob
      {...{
        blob,
        prism: { ...prism, 'show-invisibles': true, 'line-numbers': true },
      }}
    />
  ));
