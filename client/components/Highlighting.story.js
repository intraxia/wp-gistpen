import { storiesOf } from '@storybook/react';
import Kefir from 'kefir';
import { h } from 'brookjs-silt';
import Highlighting from './Highlighting';

storiesOf('Highlighting', module)
    .add('default', () => (
        <Highlighting stream$={Kefir.constant({
            demo: {
                code: `console.log('test');`,
                filename: 'test.js',
                language: 'javascript'
            },
            themes: {
                order: ['default', 'twilight'],
                dict: {
                    default: {
                        name: 'Default',
                        key: 'default',
                        selected: false
                    },
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
    ));
