import Prism from 'prismjs';
import { plugin as p1 } from '../../prism/plugins/toolbar';
import { plugin as p2 } from '../../prism/plugins/line-highlight';
import { plugin as p3 } from '../../prism/plugins/copy-to-clipboard';
import { plugin as p4 } from '../../prism/plugins/edit';
import { plugin as p5 } from '../../prism/plugins/filename';
import { PrismState } from '../../reducers';
import { GlobalsState } from '../../globals';
import { setAutoloaderPath, setTheme, togglePlugin } from '../../prism';

interface ContentWindowState {
  globals: GlobalsState;
  prism: PrismState;
}

declare global {
  interface Window {
    __GISTPEN_CONTENT__: ContentWindowState;
  }
}

p1.use();
p2.use();
p3.use();
p4.use();
p5.use();

const { __GISTPEN_CONTENT__ } = window;

setAutoloaderPath(
  (__webpack_public_path__ =
    __GISTPEN_CONTENT__.globals.url + 'resources/assets/'),
);

const promises: Array<Promise<any>> = [];

promises.push(setTheme(__GISTPEN_CONTENT__.prism.theme));

if (__GISTPEN_CONTENT__.prism['line-numbers']) {
  promises.push(togglePlugin('line-numbers', true));
}

if (__GISTPEN_CONTENT__.prism['show-invisibles']) {
  promises.push(togglePlugin('show-invisibles', true));
}

Promise.all(promises).then(() => {
  if (document.readyState !== 'loading') {
    window.requestAnimationFrame(() => Prism.highlightAll());
  } else {
    document.addEventListener('DOMContentLoaded', () => Prism.highlightAll());
  }
});
