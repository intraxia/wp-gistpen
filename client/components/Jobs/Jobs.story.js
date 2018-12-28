import { storiesOf } from '@storybook/react';
import Kefir from 'kefir';
import { h } from 'brookjs-silt';
import Jobs from './';

storiesOf('Jobs', module)
    .add('default', () => (
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
    ));
