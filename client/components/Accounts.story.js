import { storiesOf } from '@storybook/react';
import { action } from '@storybook/addon-actions';
import { Kefir } from 'brookjs';
import { Aggregator, h } from 'brookjs-silt';
import Accounts from './Accounts';

storiesOf('Accounts', module)
    .add('default', () => (
        <Aggregator action$={action$ => action$.observe(a => action('Jobs')(a))}>
            <Accounts stream$={Kefir.constant({ token: 'ancsdf' })}/>
        </Aggregator>
    ));
