import { configure } from '@storybook/react';

function loadStories() {
    const req = require.context('../client', true, /.*\.story\.js/);

    req.keys().forEach(req);
}

configure(loadStories, module);
