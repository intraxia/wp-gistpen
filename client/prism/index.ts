import Prism from 'prismjs';

// @ts-ignore
Prism.manual = true;

if (window.__webpack_public_path__ != null) {
  Prism.plugins.autoloader.languages_path = window.__webpack_public_path__;
}

Prism.languages.none = {};

type Theme = {
  use(): void;
  unuse(): void;
};

let currentTheme: Theme;

const plugins: Record<string, boolean> = {};

export const setAutoloaderPath = (path: string) =>
  (Prism.plugins.autoloader.languages_path = path);

export const setTheme = (theme: string): Promise<Theme> =>
  import(`./themes/${theme}.lazy.css`).then(
    ({ default: theme }: { default: Theme }) =>
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
        }),
      ),
  );

export const togglePlugin = (
  pluginKey: string,
  toggle: boolean,
): Promise<Theme> =>
  import(`./plugins/${pluginKey}.ts`).then(
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
        }),
      ),
  );
