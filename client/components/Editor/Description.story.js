// @flow
// @jsx h
import { storiesOf } from '@storybook/react';
import { action } from '@storybook/addon-actions';
import { Kefir } from 'brookjs';
import { h, Aggregator } from 'brookjs-silt';
import Description from './Description';

storiesOf('Description', module)
    .add('without text - not loading', () => (
        <Aggregator action$={action$ => action$.observe(a => action('Description')(a))}>
            <Description stream$={Kefir.constant({
                description: '',
                loading: false
            })}/>
        </Aggregator>
    ))
    .add('with text - not loading', () => (
        <Aggregator action$={action$ => action$.observe(a => action('Description')(a))}>
            <Description stream$={Kefir.constant({
                description: 'New Repo',
                loading: false
            })}/>
        </Aggregator>
    ))
    .add('with text - loading', () => (
        <Aggregator action$={action$ => action$.observe(a => action('Description')(a))}>
            <Description stream$={Kefir.constant({
                description: 'New Repo',
                loading: true
            })}/>
        </Aggregator>
    ));
