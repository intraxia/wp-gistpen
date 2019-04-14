import path from 'path';
import {
  devtool,
  styleRule,
  usableStyleRule,
  styleLintPlugin,
  notifierPlugin,
  copyPlugin,
  prismLanguageGenerationPlugin
} from './webpack';

export const dir = 'client';

/**
 * Mocha testing configuration.
 */
export const mocha = {
  reporter: 'spec',
  ui: 'bdd',
  requires: ['@babel/register', 'esm', 'jsdom-global/register']
};

const client = path.resolve(__dirname, dir);
const pages = path.resolve(client, 'pages');

const isProd = state => state.command.opts.env === 'production';

/**
 * Webpack build configuration.
 */
export const webpack = {
  entry: {
    settings: 'pages/settings',
    content: 'pages/content',
    editor: 'pages/edit',
    tinymce: 'pages/tinymce'
  },
  output: {
    path: 'assets/js/',
    filename: state => `[name]${isProd(state) ? '.min' : ''}.js`
  },
  modifier: (config, state) => {
    if (!isProd(state)) {
      config.devtool = devtool;
    }

    config.module.rules.push(styleRule);
    config.module.rules.push(usableStyleRule);

    config.plugins.push(styleLintPlugin);
    config.plugins.push(notifierPlugin);
    config.plugins.push(copyPlugin);
    config.plugins.push(prismLanguageGenerationPlugin);

    return config;
  }
};

export const storybook = {
  port: 9001,
  host: null,
  staticDirs: ['assets/js'],
  https: {
    enabled: false
  },
  devServer: {},
  middleware: (router, state) => router
};
