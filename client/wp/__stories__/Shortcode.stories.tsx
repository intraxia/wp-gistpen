import React from 'react';
import { storiesOf } from '@storybook/react';
import { Shortcode } from '../Shortcode';

storiesOf('Shortcode', module).add('default', () => <Shortcode blobId={123} />);
