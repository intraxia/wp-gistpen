import path from 'path';
import { BundleAnalyzerPlugin } from 'webpack-bundle-analyzer';
import {
  devtool,
  styleRule,
  usableStyleRule,
  styleLintPlugin,
  notifierPlugin,
  resolve,
  copyPlugin,
  prismLanguageGenerationPlugin,
  tsCheckPlugin
} from './webpack';

export const dir = 'client';

const client = path.resolve(__dirname, dir);
const pages = path.resolve(client, 'pages');

const isProd = state => state.env === 'production';

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

    config.resolve = {
      ...config.resolve,
      ...resolve
    };

    config.module.rules.push(styleRule);
    config.module.rules.push(usableStyleRule);

    config.plugins.push(styleLintPlugin);
    config.plugins.push(notifierPlugin);
    config.plugins.push(prismLanguageGenerationPlugin);
    config.plugins.push(tsCheckPlugin);

    if (isProd(state)) {
      config.plugins.push(copyPlugin);
    }

    if (process.env.ANALYZE_GISTPEN === 'true') {
      config.plugins.push(
        new BundleAnalyzerPlugin({
          analyzerMode: 'static',
          openAnalyzer: false
        })
      );
    }

    config.externals = {
      react: 'React',
      'react-dom': 'ReactDOM',
      '@wordpress/blocks': 'wp.blocks',
      '@wordpress/components': 'wp.components',
      '@wordpress/compose': 'wp.compose',
      '@wordpress/element': 'wp.element',
      '@wordpress/i18n': 'wp.i18n'
    };

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
