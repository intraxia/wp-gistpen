const gulp = require('gulp');
const R = require('ramda');
const webpack = require('webpack');
const webpackConfig = require('./webpack.config');
const gutil = require('gulp-util');
const path = require('path');

gulp.task('dev', ['dev:app', 'dev:tdd']);

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

gulp.task('dev:tdd', ['test:unit'], () => {
    gulp.watch([
        path.join(__dirname, '..', '/client/**/*.js'),
        path.join(__dirname, '..', '/client/**/__tests__/*.spec.js')
    ], ['test:unit']);
});
