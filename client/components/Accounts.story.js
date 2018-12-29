import { storiesOf } from '@storybook/react';
import Kefir from 'kefir';
import { h } from 'brookjs-silt';
import Accounts from './Accounts';

storiesOf('Accounts', module)
    .add('default', () => (
        <Accounts stream$={Kefir.constant({ token: 'ancsdf' })}/>
    ));
