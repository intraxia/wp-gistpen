var gulp = require('gulp');
var clean = require('gulp-clean');
var jshint = require('gulp-jshint');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var composer = require('gulp-composer');
var bower = require('gulp-bower');
var zip = require('gulp-zip');
var runs  = require('run-sequence');

var paths = {
	js: {
		pub: [
			'public/assets/vendor/SyntaxHighlighter/scripts/XRegExp.js',
			'public/assets/vendor/SyntaxHighlighter/scripts/shLegacy.js',
			'public/assets/vendor/SyntaxHighlighter/scripts/shCore.js',
			'public/assets/vendor/SyntaxHighlighter/scripts/shAutoloader.js',
			'public/assets/js/*.js'],
		admin: ['admin/assets/js/wp-gistpen-admin.js'],
		editor: ['admin/assets/js/wp-gistpen-editor.js'],
	},
	add: ['**/*.php',
		'**/*.png',
		'**/*.pot',
		'.*',
		'*.txt',
		'*.php',
		'*.json',
		'*.lock',
		'!node_modules/**',
		'!includes/**',
		'!public/assets/vendor/**'],
	build: 'build/'
};

gulp.task('dev', function () {
	// Public Javascript Files
	gulp.src(paths.js.pub)
		// Concatenate the Javascript
		.pipe(concat('wp-gistpen.min.js'))
		.pipe(gulp.dest('public/assets/js/'));
	// Admin Javascript Files
	gulp.src(paths.js.admin)
		// Concatenate the Javascript
		.pipe(concat('wp-gistpen-admin.min.js'))
		.pipe(gulp.dest('admin/assets/js/'));
	// Editor Javascript Files
	gulp.src(paths.js.editor)
		// Concatenate the Javascript
		.pipe(concat('wp-gistpen-editor.min.js'))
		.pipe(gulp.dest('admin/assets/js/'));
	// Install composer dependencies
	composer({ bin: 'composer' });
	// Install bower dependencies
	bower();
});

// Watch and regen
gulp.task('watch', ['dev'], function () {
	gulp.watch('**/*.js', ['dev']);
});

// Delete the build directory
gulp.task('clean', function() {
	return gulp.src(paths.build)
		.pipe(clean());
});

// Copy source files
gulp.task('copy', function() {
	return gulp.src(paths.add)
		.pipe(gulp.dest(paths.build));
});

gulp.task('minify', function () {
	// Public Javascript Files
	gulp.src(paths.pubjs)
		.pipe(concat('wp-gistpen.min.js'))
		.pipe(uglify('.'))
		.pipe(gulp.dest(paths.build + 'public/assets/js/'));
	// Admin Javascript Files
	return gulp.src(paths.adminjs)
		.pipe(concat('wp-gistpen-admin.min.js'))
		.pipe(uglify('.'))
		.pipe(gulp.dest(paths.build + 'admin/assets/js/'));
});

gulp.task('install', function() {
	// Install composer dependencies
	composer({bin: 'composer', cwd: process.cwd()+'/'+paths.build});
	// Install bower dependencies
	return bower({cwd: paths.build});
});

gulp.task('zip', function() {
	return gulp.src(paths.build + '**')
		.pipe(zip('wp-gistpen.zip'))
		.pipe(gulp.dest('./'));
});

gulp.task('build', function(done) {
	runs(
		'clean',
		'copy',
		['minify', 'install'],
		'zip',
		'clean',
		done);
});
