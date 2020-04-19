import Prism from '../../prism';
import { plugin as p1 } from '../../prism/plugins/toolbar';
import { plugin as p2 } from '../../prism/plugins/line-highlight';
import '../../prism/plugins/copy-to-clipboard';
import '../../prism/plugins/edit';
import '../../prism/plugins/filename';
import { GlobalsState, PrismState } from '../../reducers';

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

const { __GISTPEN_CONTENT__ } = window;

Prism.setAutoloaderPath(
  (__webpack_public_path__ =
    __GISTPEN_CONTENT__.globals.url + 'resources/assets/'),
);

const promises: Array<Promise<any>> = [];

promises.push(Prism.setTheme(__GISTPEN_CONTENT__.prism.theme));

if (__GISTPEN_CONTENT__.prism['line-numbers']) {
  promises.push(Prism.togglePlugin('line-numbers', true));
}

if (__GISTPEN_CONTENT__.prism['show-invisibles']) {
  promises.push(Prism.togglePlugin('show-invisibles', true));
}

Promise.all(promises).then(() => {
  if (document.readyState !== 'loading') {
    if (window.requestAnimationFrame) {
      window.requestAnimationFrame(Prism.highlightAll);
    } else {
      window.setTimeout(Prism.highlightAll, 16);
    }
  } else {
    document.addEventListener('DOMContentLoaded', Prism.highlightAll);
  }
});
