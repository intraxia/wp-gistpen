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
