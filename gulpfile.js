var gulp = require('gulp')
var concat = require('gulp-concat')
var composer = require('gulp-composer');

gulp.task('dev', function () {
	// Concatenate the Javascript
	gulp.src([
		'public/assets/vendor/SyntaxHighlighter/scripts/XRegExp.js',
		'public/assets/vendor/SyntaxHighlighter/scripts/shLegacy.js',
		'public/assets/vendor/SyntaxHighlighter/scripts/shCore.js',
		'public/assets/vendor/SyntaxHighlighter/scripts/shAutoloader.js',
		'public/assets/js/*.js'])
		.pipe(concat('wp-gistpen.min.js'))
		.pipe(gulp.dest('public/assets/js/'));
	gulp.src([
		'admin/assets/js/wp-gistpen-admin.js'])
		.pipe(concat('wp-gistpen-admin.min.js'))
		.pipe(gulp.dest('admin/assets/js/'));
	// Install composer dependencies
	composer({ bin: 'composer' });
})

gulp.task('watch', ['dev'], function () {
	gulp.watch('public/**/*.js')
})
