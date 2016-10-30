const gulp = require('gulp');
const R = require('ramda');
const webpack = require('webpack');
const webpackConfig = require('./webpack.config');
const gutil = require('gulp-util');

gulp.task('dev', ['dev:app']);

gulp.task('dev:app', () => {
    const webpackWatchConfig = R.clone(webpackConfig);
    webpackWatchConfig.watch = true;

    webpack(webpackWatchConfig, (err, stats) => {
        if (err) {
            throw new gutil.PluginError('[dev:app]', err);
        }

        gutil.log('[dev:app]', stats.toString({
            colors: true,
            chunkModules: false
        }));
    });
});
