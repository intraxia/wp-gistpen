import { storiesOf } from '@storybook/react';
import { Kefir } from 'brookjs';
import { h } from 'brookjs-silt';
import Header from './Header';

storiesOf('Header', module)
    .add('default', () => (
        <Header stream$={Kefir.constant({ route: 'highlighting', loading: false })}/>
    ))
    .add('loading', () => (
        <Header stream$={Kefir.constant({ route: 'highlighting', loading: true })}/>
    ));
