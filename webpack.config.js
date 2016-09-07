const webpack = require('webpack');
const src = __dirname + '/src/js/';

module.exports = {
    debug: true,
    devtool: 'sourcemap',
    entry: {
        settings: src + 'settings.js',
        web: src + 'web.js',
        post: src + 'post.js',
        tinymce: src + 'tinymce.js'
    },
    output: {
        path: 'assets/js/',
        filename: '[name].js'
    },
    module: {
        loaders: [
            {
                test: /\.js$/,
                loader: 'babel',
                include: src
            },
            {
                test: /\.hbs/,
                loader: 'handlebars'
            }
        ]
    },
    plugins: []
};
