const path = require('path');
const StyleLintPlugin = require('stylelint-webpack-plugin');
const FlowStatusWebpackPlugin = require('flow-status-webpack-plugin');
const WebpackNotifierPlugin = require('webpack-notifier');
const gutil = require('gulp-util');
const notifier = require('node-notifier');
const flowPath = require('flow-bin');

const src = path.join(__dirname, '..', 'src', 'js');
const client = path.join(__dirname, '..', 'client');
const page = path.join(client, 'page');

module.exports = {
    devtool: 'sourcemap',
    entry: {
        settings: path.join(page, 'settings'),
        content: path.join(page, 'content'),
        editor: path.join(page, 'editor'),
        tinymce: path.join(page, 'tinymce')
    },
    output: {
        path: path.join(__dirname, '..', 'assets', 'js'),
        filename: '[name].js'
    },
    module: {
        loaders: [
            {
                test: /\.js$/,
                loader: 'eslint-loader',
                exclude: /(node_modules)/,
                enforce: 'pre'
            },
            {
                test: /\.js$/,
                loader: 'babel-loader',
                include: [src, client]
            },
            {
                test: /\.hbs/,
                loader: 'handlebars-loader',
                query: {
                    helperDirs: [path.join(client, 'helpers')],
                    partialDirs: [client],
                    preventIndent: true,
                    compat: true
                }
            },
            {
                test: /\.(scss|css)$/,
                include: [
                    path.join(page, 'editor'),
                    path.join(page, 'tinymce'),
                    path.join(client, 'component')
                ],
                loaders: ['style-loader', 'css-loader', 'sass-loader']
            },
            {
                test: /\.(scss|css)$/,
                include: [
                    path.join(page, 'settings'),
                    path.join(client, 'prism'),
                    /node_modules/
                ],
                loaders: ['style-loader/useable', 'css-loader', 'sass-loader']
            }
        ]
    },
    resolve: {
        alias: {
            redux: 'redux/es',
            brookjs: 'brookjs/es'
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
