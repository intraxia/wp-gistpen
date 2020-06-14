import path from 'path';
import fs from 'fs-extra';
import StyleLintPlugin from 'stylelint-webpack-plugin';
import WebpackNotifierPlugin from 'webpack-notifier';
import CopyWebpackPlugin from 'copy-webpack-plugin';
import { BundleAnalyzerPlugin } from 'webpack-bundle-analyzer';
import DependencyExtractionWebpackPlugin from '@wordpress/dependency-extraction-webpack-plugin';

const isProd = state => state.env === 'production';

class PrismLanguageGenerationPlugin {
  slug = 'prism-lang-gen';

  src = path.join(__dirname, 'node_modules', 'prismjs', 'components.json');

  dest = path.join(__dirname, 'resources', 'languages.json');

  exclude = ['meta', 'clike', 'css-extras', 'markup-templating', 'php-extras'];

  prev = null;

  /**
   * @param {import('webpack').Compiler} compiler
   */
  apply(compiler) {
    compiler.hooks.watchRun.tapAsync(this.slug, this.cb);
    compiler.hooks.beforeRun.tapAsync(this.slug, this.cb);
  }

  cb = (compilation, callback) => this.emit(compilation, callback);

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
        plaintext: 'PlainText',
      },
      aliases: {
        plaintext: 'none',
        jinja2: 'django',
      },
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
    filename: state => `[name]${isProd(state) ? '.min' : ''}.js`,
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
            injectType: 'lazyStyleTag',
          },
        },
        'css-loader',
        'sass-loader',
      ],
    });

    config.plugins.push(
      new StyleLintPlugin({
        syntax: 'scss',
        context: path.join(__dirname, dir),
      }),
    );
    config.plugins.push(
      new WebpackNotifierPlugin({
        alwaysNotify: true,
        emoji: true,
      }),
    );
    config.plugins.push(new PrismLanguageGenerationPlugin());
    config.plugins.push(
      new DependencyExtractionWebpackPlugin({
        outputFormat: 'json',
        combineAssets: true,
        combinedOutputFile: `wp-assets${isProd(state) ? '.min' : ''}.json`,
      }),
    );

    if (isProd(state)) {
      config.plugins[0].opts.fileName = 'asset-manifest.min.json';
      config.plugins.push(
        new CopyWebpackPlugin([
          {
            from: 'node_modules/prismjs/components/*.js',
            flatten: true,
          },
        ]),
      );
    }

    if (process.env.ANALYZE_GISTPEN === 'true') {
      config.plugins.push(
        new BundleAnalyzerPlugin({
          analyzerMode: 'static',
          openAnalyzer: false,
        }),
      );
    }

    return config;
  },
};

export const jest = {
  moduleNameMapper: {
    '^.+\\.lazy\\.(css|sass|scss)$': '<rootDir>/__mocks__/lazy.js',
  },
  transformIgnorePatterns: ['^.+\\.lazy\\.(css|sass|scss)$'],
};

export const babel = {
  modifier(options) {
    return {
      ...options,
      plugins: [
        ...options.plugins,
        [
          'babel-plugin-prismjs',
          {
            plugins: ['autoloader'],
            css: true,
          },
        ],
      ],
    };
  },
};
