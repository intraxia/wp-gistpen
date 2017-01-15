const gulp = require('gulp');
const sass = require('gulp-sass');
const cssnano = require('gulp-cssnano');
const extrep = require('gulp-ext-replace');
const pot = require('gulp-wp-pot');
const sort = require('gulp-sort');

require('./copy');
require('./dev');
require('./scripts');
require('./test');

gulp.task('styles', function () {
    return gulp.src('src/scss/*.scss')
        .pipe(sass())
        .pipe(gulp.dest('assets/css'))
        .pipe(cssnano())
        .pipe(extrep('.min.css'))
        .pipe(gulp.dest('assets/css'));
});

gulp.task('translate', function () {
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

gulp.task('default', ['scripts:dev', 'copy', 'translate']);
gulp.task('build', ['scripts', 'styles', 'copy', 'translate']);
