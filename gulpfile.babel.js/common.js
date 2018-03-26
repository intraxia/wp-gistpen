const gulp = require('gulp');
const pot = require('gulp-wp-pot');
const sort = require('gulp-sort');

gulp.task('common', ['common:prism', 'common:genpot']);

gulp.task('common:prism', () =>
    gulp.src('node_modules/prismjs/components/*.js')
        .pipe(gulp.dest('assets/js/')));

gulp.task('common:genpot', () =>
    gulp.src('app/**/*.php')
        .pipe(sort())
        .pipe(pot({
            domain: 'wp-gistpen',
            destFile: 'wp-gistpen.pot',
            package: 'wp-gistpen',
            bugReport: 'http://github.com/intraxia/WP-Gistpen'
        }))
        .pipe(gulp.dest('languages/')));
