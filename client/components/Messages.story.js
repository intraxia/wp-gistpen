import { storiesOf } from '@storybook/react';
import { action } from '@storybook/addon-actions';
import { Kefir } from 'brookjs';
import { Aggregator, h } from 'brookjs-silt';
import Messages from './Messages';

storiesOf('Messages', module)
    .add('default', () => (
        <Aggregator action$={action$ => action$.observe(a => action('Messages')(a))}>
            <Messages stream$={Kefir.constant({
                job: 'Export',
                job_id: '2',
                status: 'finished',
                messages: {
                    order: ['1'],
                    dict: {
                        '1': {
                            ID: '1',
                            run_id: '2',
                            text: 'This is a message',
                            level: 'info',
                            logged_at: 'yesterday'
                        }
                    }
                }
            })}/>
        </Aggregator>
    ));
