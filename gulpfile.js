var gulp = require('gulp');
var gutil = require('gulp-util');
var path = require('path');
var sass = require('gulp-sass');
var cssnano = require('gulp-cssnano');
var extrep = require('gulp-ext-replace');
var pot = require('gulp-wp-pot');
var sort = require('gulp-sort');

var webpack = require("webpack");
var webpackConfig = require("./webpack.config.js");

gulp.task('default', ['develop']);

gulp.task('develop', ['webpack:dev', 'styles', 'copy', 'translation'], function () {
    gulp.watch('src/js/**/*.js', ['webpack:dev']);
    gulp.watch('src/js/**/*.hbs', ['webpack:dev']);
    gulp.watch('src/scss/**/*.scss', ['styles']);
    gulp.watch('app/**/*.php', ['translation']);
});

// modify webpack config options for development
var webpackDevConfig = Object.create(webpackConfig);
webpackDevConfig.devtool = "sourcemap";
webpackDevConfig.debug = true;
webpackDevConfig.output.filename = '[name].js';
var devCompiler = webpack(webpackDevConfig);

gulp.task('webpack:dev', function (callback) {
    // run webpack
    devCompiler.run(function(err, stats) {
        if(err) throw new gutil.PluginError('webpack:dev', err);
        gutil.log('[webpack:dev]', stats.toString({
            colors: true
        }));
        callback();
    });
});

gulp.task('build', ['webpack:build', 'styles', 'prism', 'ace']);

// modify some webpack config options
var webpackBuildConfig = Object.create(webpackConfig);
webpackBuildConfig.plugins = webpackBuildConfig.plugins.concat(
    new webpack.DefinePlugin({
        "process.env": {
            // This has effect on the react lib size
            "NODE_ENV": JSON.stringify('production')
        }
    }),
    new webpack.optimize.UglifyJsPlugin({minimize: true})
);

gulp.task('webpack:build', ['webpack:dev'], function(callback) {
    // run webpack
    webpack(webpackBuildConfig, function(err, stats) {
        if(err) throw new gutil.PluginError('webpack:build', err);
        gutil.log('[webpack:build]', stats.toString({
            colors: true
        }));
        callback();
    });
});

gulp.task('styles', function () {
    return gulp.src('src/scss/*.scss')
        .pipe(sass())
        .pipe(gulp.dest('assets/css'))
        .pipe(cssnano())
        .pipe(extrep('.min.css'))
        .pipe(gulp.dest('assets/css'));
});

gulp.task('copy', ['prism', 'ace']);

gulp.task('prism', ['prism:scripts', 'prism:styles']);

gulp.task('prism:scripts', function() {
    return gulp.src([
        'node_modules/prismjs/components/*.js',
        'node_modules/prismjs/plugins/line-numbers/*.js',
        'node_modules/prismjs/plugins/show-invisibles/*.js'
    ])
        .pipe(gulp.dest('assets/js/'));
});

gulp.task('prism:styles', function () {
    return gulp.src([
            'node_modules/prismjs/themes/*.css',
            'node_modules/prism-themes/themes/*.css',
            'node_modules/prismjs/plugins/line-numbers/*.css',
            'node_modules/prismjs/plugins/show-invisibles/*.css'
        ])
        .pipe(gulp.dest('assets/css'))
        .pipe(cssnano())
        .pipe(extrep('.min.css'))
        .pipe(gulp.dest('assets/css'));
});

gulp.task('ace', function () {
    return gulp.src('node_modules/ace-builds/src-min-noconflict/**')
        .pipe(gulp.dest('assets/js/ace'));
});

gulp.task('translation', function () {
    return gulp.src('app/**/*.php')
        .pipe(sort())
        .pipe(pot({
            domain: 'wp-gistpen',
            destFile: 'wp-gistpen.pot',
            package: 'wp-gistpen',
            bugReport: 'http://github.com/mAAdhaTTah/WP-Gistpen'
        }))
        .pipe(gulp.dest('languages/'));
});
