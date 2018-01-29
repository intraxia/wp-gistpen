// @flow
// @jsx h
import { storiesOf } from '@storybook/react';
import { action } from '@storybook/addon-actions';
import { Kefir } from 'brookjs';
import { h, Aggregator } from 'brookjs-silt';
import { Search } from './Search';

storiesOf('Search', module)
    .add('without results; not loading', () => (
        <Aggregator action$={action$ => action$.observe(a => action('Search')(a))}>
            <Search stream$={Kefir.constant({ loading: false, term: '', results: { order: [], dict: {} }})}/>
        </Aggregator>
    ))
    .add('with results; not loading', () => (
        <Aggregator action$={action$ => action$.observe(a => action('Search')(a))}>
            <Search stream$={Kefir.constant({
                loading: false,
                term: '',
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
        </Aggregator>
    ))
    .add('without results; loading', () => (
        <Aggregator action$={action$ => action$.observe(a => action('Search')(a))}>
            <Search stream$={Kefir.constant({ loading: true, term: '', results: { order: [], dict: {} }})}/>
        </Aggregator>
    ))
    .add('with results; loading', () => (
        <Aggregator action$={action$ => action$.observe(a => action('Search')(a))}>
            <Search stream$={Kefir.constant({
                loading: true,
                term: '',
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
        </Aggregator>
    ));
