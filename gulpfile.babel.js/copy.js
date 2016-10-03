const gulp = require('gulp');

gulp.task('copy', ['ace', 'prism']);

gulp.task('prism', function() {
    return gulp.src('node_modules/prismjs/components/*.js')
        .pipe(gulp.dest('assets/js/'));
});

gulp.task('ace', function () {
    return gulp.src('node_modules/ace-builds/src-min-noconflict/**')
        .pipe(gulp.dest('assets/js/ace'));
});
