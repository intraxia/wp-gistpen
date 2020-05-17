import path from 'path';
import fs from 'fs-extra';
import StyleLintPlugin from 'stylelint-webpack-plugin';
import WebpackNotifierPlugin from 'webpack-notifier';
import CopyWebpackPlugin from 'copy-webpack-plugin';
import { BundleAnalyzerPlugin } from 'webpack-bundle-analyzer';

const isProd = state => state.env === 'production';

class PrismLanguageGenerationPlugin {
  constructor() {
    this.src = path.join(
      __dirname,
      'node_modules',
      'prismjs',
      'components.json'
    );
    this.dest = path.join(__dirname, 'resources', 'languages.json');
    this.exclude = [
      'meta',
      'clike',
      'css-extras',
      'markup-templating',
      'php-extras'
    ];
    this.prev = null;
  }

  apply(compiler) {
    const cb = (compilation, callback) => this.emit(compilation, callback);
    compiler.plugin('watch-run', cb);
    compiler.plugin('before-run', cb);
  }

  emit(compilation, callback) {
    if (this.prev !== null) {
      return callback();
    }

    fs.readFile(this.src, (err, data) => {
      if (err) {
        throw err;
      }

      const { languages } = JSON.parse(data.toString());
      const dest = (this.prev = this.languagesToDest(languages));

      fs.outputFile(this.dest, JSON.stringify(dest, null, '  '), err => {
        if (err) {
          throw err;
        }

        callback();
      });
    });
  }

  languagesToDest(languages) {
    const dest = {
      list: {
        plaintext: 'PlainText'
      },
      aliases: {
        plaintext: 'none',
        jinja2: 'django'
      }
    };

    for (const language in languages) {
      if (this.exclude.includes(language)) {
        continue;
      }

      this.setLanguageToDest(dest, language, languages[language]);
    }

    // Alphabetize the object before stringify'ing.
    dest.list = this.sortObject(dest.list);
    dest.aliases = this.sortObject(dest.aliases);

    return dest;
  }

  setLanguageToDest(dest, language, { title, aliasTitles }) {
    // Backwards compatibility with BE
    switch (language) {
      case 'javascript':
        dest.list.js = title;
        dest.aliases.js = language;
        break;
      case 'python':
        dest.list.py = title;
        dest.aliases.py = language;
        break;
      default:
        dest.list[language] = title;
        break;
    }

    if (aliasTitles) {
      for (const alias in aliasTitles) {
        dest.list[alias] = aliasTitles[alias];
        dest.aliases[alias] = language;
      }
    }
  }

  sortObject(obj) {
    return Object.keys(obj)
      .sort()
      .reduce((acc, key) => Object.assign(acc, { [key]: obj[key] }), {});
  }
}

export const dir = 'client';

/**
 * Webpack build configuration.
 */
export const webpack = {
  entry: {
    settings: 'pages/settings',
    content: 'pages/content',
    edit: 'pages/edit',
    tinymce: 'pages/tinymce',
    block: 'pages/block',
  },
  output: {
    path: 'resources/assets/',
    filename: state => `[name]${isProd(state) ? '.min' : ''}.js`
  },
  modifier: (config, state) => {
    config.optimization.runtimeChunk = false;
    config.module.rules[2].exclude = /\.(module|lazy)\.css$/;
    config.module.rules.push({
      test: /\.lazy\.(scss|css)$/,
      use: [
        {
          loader: 'style-loader',
          options: {
            injectType: 'lazyStyleTag'
          }
        },
        'css-loader',
        'sass-loader'
      ]
    });

    config.plugins.push(
      new StyleLintPlugin({
        syntax: 'scss',
        context: path.join(__dirname, dir)
      })
    );
    config.plugins.push(
      new WebpackNotifierPlugin({
        alwaysNotify: true,
        emoji: true
      })
    );
    config.plugins.push(new PrismLanguageGenerationPlugin());

    if (isProd(state)) {
      config.plugins[0].opts.fileName = 'asset-manifest.min.json';
      config.plugins.push(
        new CopyWebpackPlugin([
          {
            from: 'node_modules/prismjs/components/*.js',
            flatten: true
          }
        ])
      );
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
