const path = require('path');
const fs = require('fs-extra');
const StyleLintPlugin = require('stylelint-webpack-plugin');
const WebpackNotifierPlugin = require('webpack-notifier');
const CopyWebpackPlugin = require('copy-webpack-plugin');

const client = path.join(__dirname, '..', 'client');

exports.lazyStyleRule = {
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
};

exports.styleLintPlugin = new StyleLintPlugin({
  syntax: 'scss',
  context: client
});

exports.notifierPlugin = new WebpackNotifierPlugin({
  alwaysNotify: true,
  emoji: true
});

exports.copyPlugin = new CopyWebpackPlugin([
  {
    from: 'node_modules/prismjs/components/*.js',
    flatten: true
  }
]);

class PrismLanguageGenerationPlugin {
  constructor() {
    this.src = path.join(
      __dirname,
      '..',
      'node_modules',
      'prismjs',
      'components.json'
    );
    this.dest = path.join(__dirname, '..', 'resources', 'languages.json');
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

exports.prismLanguageGenerationPlugin = new PrismLanguageGenerationPlugin();
