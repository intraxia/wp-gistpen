const R = require('ramda');
const gulp = require('gulp');
const gutil = require('gulp-util');
const webpack = require('webpack');
const merge = require('webpack-merge');
const webpackConfig = require('./webpack.config.js');

gulp.task('scripts', ['scripts:dev', 'scripts:build']);

gulp.task('scripts:dev', callback => {
    webpack(webpackConfig, (err, stats) => {
        if (err) {
            throw new gutil.PluginError('webpack:dev', err);
        }

        gutil.log('[webpack:dev]', stats.toString({
            colors: true
        }));
        callback();
    });
});

gulp.task('scripts:build', callback => {
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
        if (err) {
            throw new gutil.PluginError('webpack:build', err);
        }

        gutil.log('[webpack:build]', stats.toString({
            colors: true
        }));
        callback();
    });
});
