const R = require('ramda');
const gulp = require('gulp');
const gutil = require('gulp-util');

const webpack = require("webpack");
const webpackConfig = require("../webpack.config.js");

gulp.task('scripts', ['scripts:dev', 'scripts:build']);

gulp.task('scripts:dev', callback => {
    webpack(webpackConfig, function(err, stats) {
        if(err) throw new gutil.PluginError('webpack:dev', err);
        gutil.log('[webpack:dev]', stats.toString({
            colors: true
        }));
        callback();
    });
});

// modify some webpack config options
const webpackBuildConfig = R.clone(webpackConfig);
webpackBuildConfig.plugins = webpackBuildConfig.plugins.concat(
    new webpack.DefinePlugin({
        "process.env": {
            // This has effect on the react lib size
            "NODE_ENV": JSON.stringify('production')
        }
    }),
    new webpack.optimize.UglifyJsPlugin({minimize: true}),
    new webpack.optimize.DedupePlugin()
);

gulp.task('scripts:build', callback => {
    // run webpack
    webpack(webpackBuildConfig, function(err, stats) {
        if(err) throw new gutil.PluginError('webpack:build', err);
        gutil.log('[webpack:build]', stats.toString({
            colors: true
        }));
        callback();
    });
});
