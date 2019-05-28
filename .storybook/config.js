import { configure, addDecorator } from '@storybook/react';
import { withJunction } from 'brookjs-desalinate';
import Prism from '../client/prism';

Prism.setAutoloaderPath('/');

function loadStories() {
  const req = require.context('../client', true, /.*\.story\.tsx?/);

  req.keys().forEach(req);
}

addDecorator(withJunction);

configure(loadStories, module);
