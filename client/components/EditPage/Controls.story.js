// @flow
// @jsx h
import { storiesOf } from '@storybook/react';
import Kefir from 'kefir';
import { h } from 'brookjs-silt';
import Controls from './Controls';

const statuses = {
    order: ['draft', 'pending', 'private', 'publish'],
    dict: {
        draft: 'Draft',
        pending: 'Pending Review',
        private: 'Private',
        publish: 'Publish'
    }
};

const themes = {
    order: ['default', 'dark', 'funky', 'okaidia', 'tomorrow', 'twilight', 'coy'],
    dict: {
        default: 'Default',
        dark: 'Dark',
        funky: 'Funky',
        okaidia: 'Okaidia',
        tomorrow: 'Tomorrow',
        twilight: 'Twilight',
        coy: 'Coy',
    }
};

const widths = {
    order: ['1', '2', '4', '8'],
    dict: {
        '1': '1',
        '2': '2',
        '4': '4',
        '8': '8'
    }
};

const gist = {
    show: true,
    url: '#'
};

const sync = 'on';
const tabs = 'on';
const invisibles = 'on';

storiesOf('Controls', module)
    .add('default', () => (
        <div id="wpbody">
            <Controls stream$={Kefir.constant({
                statuses,
                themes,
                widths,
                gist,
                sync,
                tabs,
                invisibles,
                selectedTheme: 'twilight',
                selectedStatus: 'publish',
                selectedWidth: '4'
            })}/>
        </div>
    ));
