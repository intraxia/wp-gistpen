const gulp = require('gulp');
const merge = require('webpack-merge');
const webpack = require('webpack');
const webpackConfig = require('./webpack.config');
const gutil = require('gulp-util');
const { Server } = require('karma');

gulp.task('dev', ['dev:app', 'dev:tdd']);

gulp.task('dev:app', ['common'], () => {
    const webpackWatchConfig = merge(webpackConfig, {
        watch: true
    });

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

gulp.task('dev:tdd', done => {
    const server = new Server({
        configFile: __dirname + '/karma.conf.js'
    }, () => {
        gutil.log('Tests complete');

        done();
    });

    server.start();
});
