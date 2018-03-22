import { storiesOf } from '@storybook/react';
import { action } from '@storybook/addon-actions';
import { Kefir } from 'brookjs';
import { Aggregator, h } from 'brookjs-silt';
import Jobs from './Jobs';

storiesOf('Jobs', module)
    .add('default', () => (
        <Aggregator action$={action$ => action$.observe(a => action('Jobs')(a))}>
            <Jobs stream$={Kefir.constant({
                jobs: {
                    order: ['1'],
                    dict: {
                        '1': {
                            ID: '1',
                            name: 'Export',
                            slug: 'export',
                            description: 'Export things',
                            status: 'idle',
                            rest_url: '',
                            runs_url: '',
                        }
                    }
                }
            })}/>
        </Aggregator>
    ));
