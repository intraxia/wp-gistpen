import { storiesOf } from '@storybook/react';
import React from 'react';
import Controls from './Controls';

const statuses = [
  { slug: 'draft', name: 'Draft' },
  { slug: 'pending', name: 'Pending Review' },
  { slug: 'private', name: 'Private' },
  { slug: 'publish', name: 'Publish' }
];

const themes = [
  { slug: 'default', name: 'Default' },
  { slug: 'dark', name: 'Dark' },
  { slug: 'funky', name: 'Funky' },
  { slug: 'okaidia', name: 'Okaidia' },
  { slug: 'tomorrow', name: 'Tomorrow' },
  { slug: 'twilight', name: 'Twilight' },
  { slug: 'coy', name: 'Coy' }
];

const widths = [
  { slug: '1', name: '1' },
  { slug: '2', name: '2' },
  { slug: '4', name: '4' },
  { slug: '8', name: '8' }
];

const gist = {
  show: true,
  url: '#'
};

const sync = 'on';
const tabs = 'on';
const invisibles = 'on';

storiesOf('Controls', module).add('default', () => (
  <div id="wpbody">
    <Controls
      {...{
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
      }}
    />
  </div>
));
