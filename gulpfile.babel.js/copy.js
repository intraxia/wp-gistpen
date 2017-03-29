const gulp = require('gulp');

gulp.task('copy', ['prism']);

gulp.task('prism', function() {
    return gulp.src('node_modules/prismjs/components/*.js')
        .pipe(gulp.dest('assets/js/'));
});
