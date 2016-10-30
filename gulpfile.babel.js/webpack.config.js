const path = require('path');
const WebpackNotifierPlugin = require('webpack-notifier');
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
                loader: 'eslint',
                exclude: /(node_modules)/,
                enforce: 'pre'
            },
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
                include: path.join(client, 'editor'),
                loaders: ['style', 'css', 'sass']
            },
            {
                test: /\.(scss|css)$/,
                include: [
                    path.join(client, 'settings'),
                    path.join(client, 'prism'),
                    /node_modules/
                ],
                loaders: ['style/useable', 'css', 'sass']
            }
        ]
    },
    resolve: {
        alias: {
            kefir: 'kefir/src',
            redux: 'redux/es'
        },
        mainFields: ['jsnext:main', 'browser', 'main']
    },
    plugins: [
        new WebpackNotifierPlugin({ alwaysNotify: true })
    ]
};
