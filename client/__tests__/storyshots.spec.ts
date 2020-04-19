import initStoryshots from '@storybook/addon-storyshots';
import { render } from '@testing-library/react';
import { RootJunction } from 'brookjs';
import { ReactElement } from 'react';

initStoryshots({
  framework: 'react',
  renderer: (element: ReactElement) =>
    render(element, {
      wrapper: RootJunction as any,
    }),
  snapshotSerializers: [
    {
      print: (val, serialize) => serialize(val.container.firstChild),
      test: val => val && val.hasOwnProperty('container'),
    },
  ],
});
