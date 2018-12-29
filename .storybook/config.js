import { configure, addDecorator } from '@storybook/react';
import { withJunction } from 'brookjs-desalinate';

function loadStories() {
    const req = require.context('../client', true, /.*\.story\.js/);

    req.keys().forEach(req);
}

addDecorator(withJunction);

configure(loadStories, module);
