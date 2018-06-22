// @flow
// @jsx h
import { storiesOf } from '@storybook/react';
import { action } from '@storybook/addon-actions';
import { Kefir } from 'brookjs';
import { h, Aggregator } from 'brookjs-silt';
import Instance from './index';

storiesOf('Instance', module)
    .add('default', () => (
        <Aggregator action$={action$ => action$.observe(a => action('Instance')(a))}>
            <div id="wpbody">
                <Instance stream$={Kefir.constant({
                    code: "console.log('hello')",
                    filename: 'storybook.js',
                    cursor: false,
                    theme: 'twilight',
                    invisibles: 'off',
                    languages: {
                        order: ['js', 'python'],
                        dict: {
                            js: 'JavaScript',
                            python: 'Python'
                        }
                    },
                    language: 'js'
                })}/>
            </div>
        </Aggregator>
    ));
