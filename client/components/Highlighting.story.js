import { storiesOf } from '@storybook/react';
import { action } from '@storybook/addon-actions';
import { Kefir } from 'brookjs';
import { Aggregator, h } from 'brookjs-silt';
import Highlighting from './Highlighting';

storiesOf('Highlighting', module)
    .add('default', () => (
        <Aggregator action$={action$ => action$.observe(a => action('Highlighting')(a))}>
            <Highlighting stream$={Kefir.constant({
                demo: {
                    code: `console.log('test');`,
                    filename: 'test.js',
                    language: 'javascript'
                },
                themes: {
                    order: ['twilight'],
                    dict: {
                        twilight: {
                            name: 'Twilight',
                            key: 'twilight',
                            selected: true
                        }
                    }
                },
                'line-numbers': true,
                'show-invisibles': true
            })}/>
        </Aggregator>
    ));
