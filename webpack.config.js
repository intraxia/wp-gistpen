const webpack = require('webpack');
const src = __dirname + '/src/js/';
const client = __dirname + '/client/';

module.exports = {
    debug: true,
    devtool: 'sourcemap',
    entry: {
        settings: client + 'settings/index.js',
        web: src + 'web.js',
        post: src + 'post.js',
        tinymce: src + 'tinymce.js'
    },
    output: {
        path: 'assets/js/',
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
                include: src
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
                    helperDirs: [client + 'helpers'],
                    partialDirs: [client],
                    compat: true
                }
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
    plugins: []
};
