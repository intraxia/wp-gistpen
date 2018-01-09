const gulp = require('gulp');
const gutil = require('gulp-util');
const webpack = require('webpack');
const merge = require('webpack-merge');
const webpackConfig = require('./webpack.config.js');

gulp.task('scripts', ['scripts:dev', 'scripts:build']);

gulp.task('scripts:dev', callback => {
    webpack(webpackConfig, (err, stats) => {
        gutil.log('[webpack:dev]', stats.toString({
            colors: true
        }));

        if (err) {
            callback(new gutil.PluginError('webpack:dev', err));
        } else {
            callback();
        }
    });
});

gulp.task('scripts:build', ['scripts:dev'],callback => {
    process.env.NODE_ENV = 'production';

    const webpackBuildConfig = merge(webpackConfig, {
        devtool: '',
        output: {
            filename: '[name].min.js',
            chunkFilename: '[id].min.js'
        },
        plugins: [
            new webpack.DefinePlugin({
                'process.env': {
                    'NODE_ENV': JSON.stringify('production')
                }
            }),
            new webpack.optimize.UglifyJsPlugin({ minimize: true })
        ]
    });

    webpack(webpackBuildConfig, (err, stats) => {
        gutil.log('[webpack:build]', stats.toString({
            colors: true
        }));

        if (err) {
            callback(new gutil.PluginError('webpack:dev', err));
        } else {
            callback();
        }
    });
});
