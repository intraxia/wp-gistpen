// @flow
// @jsx h
import { storiesOf } from '@storybook/react';
import Kefir from 'kefir';
import { h } from 'brookjs-silt';
import Description from './Description';

storiesOf('Description', module)
    .add('without text - not loading', () => (
        <Description stream$={Kefir.constant({
            description: '',
            loading: false
        })}/>
    ))
    .add('with text - not loading', () => (
        <Description stream$={Kefir.constant({
            description: 'New Repo',
            loading: false
        })}/>
    ))
    .add('with text - loading', () => (
        <Description stream$={Kefir.constant({
            description: 'New Repo',
            loading: true
        })}/>
    ));
