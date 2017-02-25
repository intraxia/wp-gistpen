const path = require('path');
const WebpackNotifierPlugin = require('webpack-notifier');
const FlowStatusWebpackPlugin = require('flow-status-webpack-plugin');
const gutil = require('gulp-util');
const notifier = require('node-notifier');

const src = path.join(__dirname, '..', 'src', 'js');
const client = path.join(__dirname, '..', 'client');

module.exports = {
    devtool: 'sourcemap',
    entry: {
        settings: path.join(client, 'settings'),
        content: path.join(client, 'content'),
        editor: path.join(client, 'editor'),
        tinymce: path.join(src, 'tinymce')
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
                include: path.join(client, 'editor'),
                loaders: ['style-loader', 'css-loader', 'sass-loader']
            },
            {
                test: /\.(scss|css)$/,
                include: [
                    path.join(client, 'settings'),
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
            onSuccess: stdout => {
                gutil.log('[webpack:flow]', stdout);

                notifier.notify({ title: 'Flow', message: 'Flow passed' });
            },
            onError: stdout => {
                gutil.log('[webpack:flow]', stdout);

                notifier.notify({ title: 'Flow', message: 'Flow failed' });
            }
        }),
        new WebpackNotifierPlugin({ alwaysNotify: true })
    ]
};
