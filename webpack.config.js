const path = require('path');
const WebpackNotifierPlugin = require('webpack-notifier');
const src = path.join(__dirname, 'src', 'js');
const client = path.join(__dirname, 'client');

module.exports = {
    debug: true,
    devtool: 'sourcemap',
    entry: {
        settings: path.join(client, 'settings'),
        content: path.join(client, 'content'),
        post: path.join(src, 'post'),
        tinymce: path.join(src, 'tinymce')
    },
    output: {
        path: path.join(__dirname, 'assets', 'js'),
        filename: '[name].js'
    },
    module: {
        preLoaders: [
            {
                test: /\.js$/,
                loader: 'eslint',
                exclude: /(node_modules)/
            }
        ],
        loaders: [
            {
                test: /\.js$/,
                loader: 'babel',
                include: [src, client]
            },
            {
                test: /\.js$/,
                loader: 'babel',
                include: /node_modules\/(redux|brookjs|kefir)/,
                query: {
                    cacheDirectory: true
                }
            },
            {
                test: /\.hbs/,
                loader: 'handlebars',
                query: {
                    helperDirs: [path.join(client, 'helpers')],
                    partialDirs: [client],
                    preventIndent: true,
                    compat: true
                }
            },
            {
                test: /\.(scss|css)$/,
                loaders: ['style/useable', 'css', 'sass']
            }
        ]
    },
    resolve: {
        alias: {
            kefir: 'kefir/src',
            redux: 'redux/es'
        },
        mainFields: ['jsnext:main', 'browser', 'main'],
        modulesDirectories: ['node_modules']
    },
    plugins: [
        new WebpackNotifierPlugin({ alwaysNotify: true })
    ]
};
