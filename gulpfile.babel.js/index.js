const gulp = require('gulp');
const path = require('path');
const sass = require('gulp-sass');
const cssnano = require('gulp-cssnano');
const extrep = require('gulp-ext-replace');
const pot = require('gulp-wp-pot');
const sort = require('gulp-sort');

require('./scripts');

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

gulp.task('default', ['scripts:dev']);
gulp.task('build', ['scripts', 'styles', 'prism', 'ace']);
