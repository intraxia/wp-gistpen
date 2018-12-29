import { storiesOf } from '@storybook/react';
import Kefir from 'kefir';
import { h } from 'brookjs-silt';
import Messages from './Messages';

storiesOf('Messages', module)
    .add('default', () => (
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
    ));
