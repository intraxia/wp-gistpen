// @todo dedupe from main webpack configuration
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
