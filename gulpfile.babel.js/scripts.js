const R = require('ramda');
const gulp = require('gulp');
const gutil = require('gulp-util');

const webpack = require('webpack');
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
    const webpackBuildConfig = R.clone(webpackConfig);
    delete webpackBuildConfig.debug;
    delete webpackBuildConfig.devtool;
    webpackBuildConfig.output.filename = '[name].min.js';
    webpackBuildConfig.plugins = webpackBuildConfig.plugins.concat(
        new webpack.DefinePlugin({
            'process.env': {
                'NODE_ENV': JSON.stringify('production')
            }
        }),
        new webpack.optimize.UglifyJsPlugin({ minimize: true }),
        new webpack.optimize.DedupePlugin()
    );

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
