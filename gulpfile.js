var gulp = require('gulp');
var glob = require('glob');
var fs = require('fs');
var Q = require('Q');
var path = require('path');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var minify = require('gulp-minify-css');
var sass = require('gulp-sass');
var extrep = require('gulp-ext-replace');
var browserify = require('browserify');
var source = require('vinyl-source-stream');
var buffer = require('vinyl-buffer');
var pot = require('gulp-wp-pot');
var sort = require('gulp-sort');

gulp.task('default', ['scripts', 'styles', 'ace', 'watch']);

gulp.task('watch', function () {
    gulp.watch('src/js/**/*.js', ['scripts']);
    gulp.watch('src/scss/**/*.scss', ['styles']);
});

gulp.task('build', ['scripts', 'styles', 'ace']);

gulp.task('scripts',function () {
    var promises = [];

    ['post', 'tinymce', 'settings', 'web'].forEach(function (file) {
        var defer = Q.defer();
        var pipeline = browserify({
            entries: 'src/js/' + file + '.js'
        })
            .transform('hbsfy', {traverse: true})
            .bundle()
            .pipe(source(file + '.js'))
            .pipe(buffer())
            .pipe(gulp.dest('assets/js'))
            .pipe(uglify())
            .pipe(concat(file + '.min.js'))
            .pipe(gulp.dest('assets/js'));
        pipeline.on('end', function () {
            defer.resolve();
        });
        promises.push(defer.promise);
    });

    return Q.all(promises);
});

gulp.task('styles', function () {
    return gulp.src([
        'src/scss/*.scss',
        'node_modules/prismjs/themes/*.css',
        'node_modules/prismjs/plugins/line-numbers/*.css'
    ])
        .pipe(sass())
        .pipe(gulp.dest('assets/css'))
        .pipe(minify())
        .pipe(extrep('.min.css'))
        .pipe(gulp.dest('assets/css'));
});

gulp.task('ace', function () {
    return gulp.src('bower_components/ace-builds/src-min-noconflict/**')
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
