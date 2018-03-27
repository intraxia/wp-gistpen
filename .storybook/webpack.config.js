const path = require('path');
const StyleLintPlugin = require('stylelint-webpack-plugin');
const WebpackNotifierPlugin = require('webpack-notifier');
const flowPlugin = require('../config/flowPlugin');

const client = path.join(__dirname, '..', 'client');
const pages = path.join(client, 'pages');

module.exports = {
    module: {
        rules: [
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
            'brookjs-silt': 'brookjs-silt/es',
        }
    },
    plugins: [
        flowPlugin,
        new StyleLintPlugin({
            syntax: 'scss'
        }),
        new WebpackNotifierPlugin({ alwaysNotify: true })
    ]
};
