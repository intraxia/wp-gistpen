import Prism from 'prismjs/components/prism-core';
import 'prismjs/plugins/autoloader/prism-autoloader';

// Prism highlights automatically by default.
document.removeEventListener('DOMContentLoaded', Prism.highlightAll);

// eslint-disable-next-line camelcase
if (window.__webpack_public_path__ != null) {
  Prism.plugins.autoloader.languages_path = window.__webpack_public_path__;
}

Prism.languages.none = {};

type Theme = {
  use(): void;
  unuse(): void;
};

let currentTheme: Theme;

const plugins = {} as { [key: string]: boolean };

const extension = {
  setAutoloaderPath: (path: string) =>
    (Prism.plugins.autoloader.languages_path = path),
  setTheme: (theme: string) =>
    import(`./themes/${theme}.js`).then(
      ({ theme }: { theme: Theme }) =>
        new Promise(resolve =>
          requestAnimationFrame(() => {
            if (currentTheme !== theme) {
              if (currentTheme) {
                currentTheme.unuse();
              }

              theme.use();

              currentTheme = theme;
            }

            resolve(currentTheme);
          })
        )
    ),
  togglePlugin: (pluginKey: string, toggle: boolean) =>
    import(`./plugins/${pluginKey}.js`).then(
      ({ plugin }) =>
        new Promise(resolve =>
          requestAnimationFrame(() => {
            if (toggle && !plugins[pluginKey]) {
              plugin.use();
              plugins[pluginKey] = true;
            }

            if (!toggle && plugins[pluginKey]) {
              plugin.unuse();
              plugins[pluginKey] = false;
            }

            resolve(plugin);
          })
        )
    )
};

export default Object.assign({}, Prism, extension);
