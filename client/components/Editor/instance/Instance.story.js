// @flow
// @jsx h
import { storiesOf } from '@storybook/react';
import Kefir from 'kefir';
import { h } from 'brookjs-silt';
import Instance from './index';

storiesOf('Instance', module)
    .add('default', () => (
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
    ));
