import { storiesOf } from '@storybook/react';
import { action } from '@storybook/addon-actions';
import { Kefir } from 'brookjs';
import { Aggregator, h } from 'brookjs-silt';
import Runs from './Runs';

storiesOf('Runs', module)
    .add('default', () => (
        <Aggregator action$={action$ => action$.observe(a => action('Runs')(a))}>
            <Runs stream$={Kefir.constant({
                name: 'Export',
                status: 'idle',
                runs: {
                    order: ['1'],
                    dict: {
                        '1': {
                            ID: '1',
                            status: 'finished',
                            scheduled_at: 'Yesterday',
                            started_at: 'Today',
                            finished_at: 'Tomorrow',
                            job: 'export',
                            rest_url: '',
                            job_url: '',
                            console_url: ''
                        }
                    }
                }
            })}/>
        </Aggregator>
    ));
