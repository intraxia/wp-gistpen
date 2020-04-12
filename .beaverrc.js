import { BundleAnalyzerPlugin } from 'webpack-bundle-analyzer';
import {
  lazyStyleRule,
  styleLintPlugin,
  notifierPlugin,
  copyPlugin,
  prismLanguageGenerationPlugin
} from './webpack';

export const dir = 'client';

const isProd = state => state.env === 'production';

/**
 * Webpack build configuration.
 */
export const webpack = {
  entry: {
    settings: 'pages/settings',
    content: 'pages/content',
    edit: 'pages/edit',
    tinymce: 'pages/tinymce'
  },
  output: {
    path: 'resources/assets/',
    filename: state => `[name]${isProd(state) ? '.min' : ''}.js`
  },
  modifier: (config, state) => {
    config.optimization.runtimeChunk = false;
    config.module.rules[2].exclude = /\.(module|lazy)\.css$/;
    config.module.rules.push(lazyStyleRule);

    config.plugins.push(styleLintPlugin);
    config.plugins.push(notifierPlugin);
    config.plugins.push(prismLanguageGenerationPlugin);

    if (isProd(state)) {
      config.plugins[0].opts.fileName = 'asset-manifest.min.json';
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

// @TODO(mAAdhaTTah) remove dupes from internals
export const jest = {
  moduleNameMapper: {
    '^react-native$': 'react-native-web',
    '^.+\\.module\\.(css|sass|scss)$': 'identity-obj-proxy',
    'react-syntax-highlighter/dist/esm/(.*)':
      'react-syntax-highlighter/dist/cjs/$1',
    '@babel/runtime/helpers/esm/(.*)': '@babel/runtime/helpers/$1',
    '^.+\\.lazy\\.(css|sass|scss)$': '<rootDir>/__mocks__/lazy.js'
  },
  transformIgnorePatterns: [
    '[/\\\\]node_modules[/\\\\].+\\.(js|jsx|ts|tsx)$',
    '^.+\\.module\\.(css|sass|scss)$',
    '^.+\\.lazy\\.(css|sass|scss)$'
  ]
};
