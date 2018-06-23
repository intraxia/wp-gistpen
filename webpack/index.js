const path = require('path');
const fs = require('fs-extra');
const StyleLintPlugin = require('stylelint-webpack-plugin');
const WebpackNotifierPlugin = require('webpack-notifier');
const FlowStatusWebpackPlugin = require('flow-status-webpack-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const notifier = require('node-notifier');
const flowPath = require('flow-bin');

const client = path.join(__dirname, '..', 'client');
const pages = path.join(client, 'pages');

exports.devtool = 'sourcemap';

exports.eslintRule = {
    test: /\.js$/,
    use: ['eslint-loader'],
    include: client,
    enforce: 'pre'
};

exports.babelRule = {
    test: /\.js$/,
    use: ['babel-loader'],
    include: [
        client,
        path.join(__dirname, '..', 'node_modules', 'diffhtml')
    ]
};

exports.styleRule = {
    test: /\.(scss|css)$/,
    include: [
        path.join(pages, 'editor'),
        path.join(pages, 'tinymce'),
        path.join(client, 'components')
    ],
    use: [{
        loader: 'style-loader',
        options: {
            hmr: false
        }
    }, 'css-loader', 'sass-loader']
};

exports.usableStyleRule = {
    test: /\.(scss|css)$/,
    include: [
        path.join(pages, 'settings'),
        path.join(client, 'prism'),
        /node_modules/
    ],
    use: [{
        loader: 'style-loader/useable',
        options: {
            hmr: false
        }
    }, 'css-loader', 'sass-loader']
};

exports.resolve = {
    alias: {
        redux: 'redux/es',
        brookjs: 'brookjs/es',
        'brookjs-silt': 'brookjs-silt/es'
    }
};

exports.styleLintPlugin = new StyleLintPlugin({
    syntax: 'scss'
});

exports.notifierPlugin = new WebpackNotifierPlugin({
    alwaysNotify: true
});

exports.copyPlugin = new CopyWebpackPlugin([{
    from: 'node_modules/prismjs/components/*.js',
    flatten: true,
}]);

const flowOut = msg => stdout => {
    console.log(stdout); // eslint-disable-line no-console

    notifier.notify({ title: 'Flow', message: msg });
};

exports.flowPlugin = new FlowStatusWebpackPlugin({
    binaryPath: flowPath,
    onSuccess: flowOut('Flow passed'),
    onError: flowOut('Flow failed')
});

class PrismLanguageGenerationPlugin {
    constructor() {
        this.src = path.join(
            __dirname,
            '..',
            'node_modules',
            'prismjs',
            'components.json'
        );
        this.dest = path.join(
            __dirname,
            '..',
            'config',
            'languages.json'
        );
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
            const dest = this.prev = this.languagesToDest(languages);

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
        return Object.keys(obj).sort()
            .reduce((acc, key) =>
                Object.assign(acc, { [key]: obj[key] }), {});
    }
}

exports.prismLanguageGenerationPlugin = new PrismLanguageGenerationPlugin();
