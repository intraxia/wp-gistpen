import Prism from 'prismjs';
import langs from '../../resources/languages.json';

// @ts-ignore
Prism.manual = true;
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

export const isAlias = (x: string): x is keyof typeof langs.aliases =>
  x in langs.aliases;

export const prismSlug = (slug: string): string =>
  isAlias(slug) ? langs.aliases[slug] : slug;
