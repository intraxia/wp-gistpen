import { storiesOf } from '@storybook/react';
import { h } from 'brookjs-silt';
import Loader from './Loader';

storiesOf('Loader', module)
    .add('default', () => <Loader text={'Loading...'} />);
