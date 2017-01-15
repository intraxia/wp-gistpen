const gulp = require('gulp');
const ava = require('gulp-ava');

gulp.task('test', ['test:unit']);

gulp.task('test:unit', () => {
    process.env.BABEL_ENV = 'test';

    return gulp.src('../client/**/__tests__/*.spec.js')
        .pipe(ava({ verbose: true }));
});
