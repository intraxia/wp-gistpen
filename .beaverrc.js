import path from 'path';
import FlowStatusWebpackPlugin from 'flow-status-webpack-plugin';
import StyleLintPlugin from 'stylelint-webpack-plugin';
import WebpackNotifierPlugin from 'webpack-notifier';
import CopyWebpackPlugin from 'copy-webpack-plugin';
import flowPath from 'flow-bin';
import notifier from 'node-notifier';

export const dir = 'client';

/**
 * Mocha testing configuration.
 */
export const mocha = {
    reporter: 'spec',
    ui: 'bdd',
    requires: [
        'babel-register',
        'esm',
        'jsdom-global/register'
    ]
};

const client = path.resolve(__dirname, dir);
const pages = path.resolve(client, 'pages');

/**
 * Webpack build configuration.
 */
export const webpack = {
    entry: {
        settings: 'pages/settings',
        content: 'pages/content',
        editor: 'pages/edit',
        tinymce: 'pages/tinymce'
    },
    output: {
        path: 'assets/js/',
        filename: '[name].js'
    },
    modifier: (config, state) => {
        config.module.rules.push({
            test: /\.(scss|css)$/,
            include: [
                path.resolve(pages, 'editor'),
                path.resolve(pages, 'tinymce'),
                path.resolve(client, 'component'),
            ],
            use: [{
                loader: 'style-loader',
                options: {
                    hmr: false
                }
            }, 'css-loader', 'sass-loader']
        });
        config.module.rules.push({
            test: /\.(scss|css)$/,
            include: [
                path.resolve(pages, 'settings'),
                /prism/
            ],
            use: [{
                loader: 'style-loader/useable',
                options: {
                    hmr: false
                }
            }, 'css-loader', 'sass-loader']
        });

        config.plugins.push(new FlowStatusWebpackPlugin({
            binaryPath: flowPath,
            onSuccess: stdout => {
                console.log('[webpack:flow]', stdout);

                notifier.notify({title: 'Flow', message: 'Flow passed'});
            },
            onError: stdout => {
                console.log('[webpack:flow]', stdout);

                notifier.notify({title: 'Flow', message: 'Flow failed'});
            }
        }));
        config.plugins.push(new StyleLintPlugin({ syntax: 'scss' }));
        config.plugins.push(new WebpackNotifierPlugin({alwaysNotify: true}));
        config.plugins.push(new CopyWebpackPlugin([{
            from: 'node_modules/prismjs/components/*.js',
            flatten: true,
        }]));

        return config;
    }
};

export const storybook = {
    port: 9001,
    host: null,
    staticDirs: ['assets/js'],
    https: {
        enabled: false
    },
    devServer: {},
    middleware: (router, state) => router
};
