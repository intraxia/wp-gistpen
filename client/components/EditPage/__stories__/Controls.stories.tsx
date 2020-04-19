import { storiesOf } from '@storybook/react';
import React from 'react';
import Controls from '../Controls';

const statuses = [
  { slug: 'draft', name: 'Draft' },
  { slug: 'pending', name: 'Pending Review' },
  { slug: 'private', name: 'Private' },
  { slug: 'publish', name: 'Publish' },
];

const themes = [
  { slug: 'default', name: 'Default' },
  { slug: 'dark', name: 'Dark' },
  { slug: 'funky', name: 'Funky' },
  { slug: 'okaidia', name: 'Okaidia' },
  { slug: 'tomorrow', name: 'Tomorrow' },
  { slug: 'twilight', name: 'Twilight' },
  { slug: 'coy', name: 'Coy' },
  { slug: 'cb', name: 'CB' },
  { slug: 'ghcolors', name: 'GHColors' },
  { slug: 'pojoaque', name: 'Projoaque' },
  { slug: 'xonokai', name: 'Xonokai' },
  { slug: 'base16-ateliersulphurpool-light', name: 'Ateliersulphurpool-Light' },
  { slug: 'hopscotch', name: 'Hopscotch' },
  { slug: 'atom-dark', name: 'Atom Dark' },
  { slug: 'duotone-dark', name: 'Duotone Dark' },
  { slug: 'duotone-sea', name: 'Duotone Sea' },
  { slug: 'duotone-space', name: 'Duotone Space' },
  { slug: 'duotone-earth', name: 'Duotone Earth' },
  { slug: 'duotone-forest', name: 'Duotone Forest' },
  { slug: 'duotone-light', name: 'Duotone Light' },
  { slug: 'vs', name: 'VS' },
  { slug: 'darcula', name: 'Darcula' },
  { slug: 'a11y-dark', name: 'a11y Dark' },
];

const widths = [
  { slug: '1', name: '1' },
  { slug: '2', name: '2' },
  { slug: '4', name: '4' },
  { slug: '8', name: '8' },
];

const gist = {
  show: true,
  url: '#',
};

const sync = 'on';
const tabs = 'off';
const invisibles = 'on';

const stories = storiesOf('Controls', module);

themes.forEach(theme => {
  stories.add(theme.name, () => (
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
          selectedTheme: theme.slug,
          selectedStatus: 'publish',
          selectedWidth: '4',
        }}
      />
    </div>
  ));
});
