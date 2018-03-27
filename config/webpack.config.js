const path = require('path');
const StyleLintPlugin = require('stylelint-webpack-plugin');
const FlowStatusWebpackPlugin = require('flow-status-webpack-plugin');
const WebpackNotifierPlugin = require('webpack-notifier');
const gutil = require('gulp-util');
const notifier = require('node-notifier');
const flowPath = require('flow-bin');

const client = path.join(__dirname, '..', 'client');
const pages = path.join(client, 'pages');

module.exports = {
    devtool: 'sourcemap',
    entry: {
        settings: path.join(pages, 'settings'),
        content: path.join(pages, 'content'),
        editor: path.join(pages, 'edit'),
        tinymce: path.join(pages, 'tinymce')
    },
    output: {
        path: path.join(__dirname, '..', 'assets', 'js'),
        filename: '[name].js'
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                use: ['eslint-loader'],
                include: client,
                enforce: 'pre'
            },
            {
                test: /\.js$/,
                use: ['babel-loader'],
                include: [
                    client,
                    path.join(__dirname, '..', 'node_modules', 'diffhtml')
                ]
            },
            {
                test: /\.hbs/,
                use: [{
                    loader: 'handlebars-loader',
                    query: {
                        helperDirs: [path.join(client, 'helpers')],
                        partialDirs: [client],
                        preventIndent: true,
                        compat: true
                    }
                }]
            },
            {
                test: /\.(scss|css)$/,
                include: [
                    path.join(pages, 'editor'),
                    path.join(pages, 'tinymce'),
                    path.join(client, 'component')
                ],
                use: [{
                    loader: 'style-loader',
                    options: {
                        hmr: false
                    }
                }, 'css-loader', 'sass-loader']
            },
            {
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
            }
        ]
    },
    resolve: {
        alias: {
            redux: 'redux/es',
            brookjs: 'brookjs/es',
            'brookjs-silt': 'brookjs-silt/es'
        },
        mainFields: ['jsnext:main', 'browser', 'main']
    },
    plugins: [
        new FlowStatusWebpackPlugin({
            binaryPath: flowPath,
            onSuccess: stdout => {
                gutil.log('[webpack:flow]', stdout);

                notifier.notify({ title: 'Flow', message: 'Flow passed' });
            },
            onError: stdout => {
                gutil.log('[webpack:flow]', stdout);

                notifier.notify({ title: 'Flow', message: 'Flow failed' });
            }
        }),
        new StyleLintPlugin({
            syntax: 'scss'
        }),
        new WebpackNotifierPlugin({ alwaysNotify: true })
    ]
};
