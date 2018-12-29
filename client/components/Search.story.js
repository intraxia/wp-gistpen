// @flow
// @jsx h
import { storiesOf } from '@storybook/react';
import Kefir from 'kefir';
import { h } from 'brookjs-silt';
import { Search } from './Search';

storiesOf('Search', module)
    .add('without results; not loading', () => (
        <Search stream$={Kefir.constant({ loading: false, term: '', results: { order: [], dict: {} } })}/>
    ))
    .add('with results; not loading', () => (
        <Search stream$={Kefir.constant({
            loading: false,
            term: 'test',
            results: {
                order: ['2', '1'],
                dict: {
                    '1': {
                        filename: 'test1.js'
                    },
                    '2': {
                        filename: 'test2.js'
                    }
                }
            }
        })}/>
    ))
    .add('without results; loading', () => (
        <Search stream$={Kefir.constant({ loading: true, term: '', results: { order: [], dict: {} } })}/>
    ))
    .add('with results; loading', () => (
        <Search stream$={Kefir.constant({
            loading: true,
            term: 'test',
            results: {
                order: ['2', '1'],
                dict: {
                    '1': {
                        filename: 'test1.js'
                    },
                    '2': {
                        filename: 'test2.js'
                    }
                }
            }
        })}/>
    ));
