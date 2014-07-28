var gulp = require('gulp'),
	rimraf = require('rimraf'),
	jshint = require('gulp-jshint'),
	concat = require('gulp-concat'),
	uglify = require('gulp-uglify'),
	composer = require('gulp-composer'),
	bower = require('gulp-bower'),
	zip = require('gulp-zip'),
	sass = require('gulp-sass'),
	gulpif = require('gulp-if'),
	runs = require('run-sequence'),
	merge = require('merge-stream'),
	print = require('gulp-print'),
	building = false;

var paths = {
	js: {
		public: {
			files: [
				'public/assets/vendor/SyntaxHighlighter/scripts/XRegExp.js',
				'public/assets/vendor/SyntaxHighlighter/scripts/shLegacy.js',
				'public/assets/vendor/SyntaxHighlighter/scripts/shCore.js',
				'public/assets/vendor/SyntaxHighlighter/scripts/shAutoloader.js',
				'public/assets/js/*.js',
				'!public/assets/js/wp-gistpen.min.js'],
			output: {
				filename: 'wp-gistpen.min.js',
				dir: 'public/assets/js/'
			},
		},
		admin: {
			files: ['admin/assets/js/wp-gistpen-admin.js'],
			output: {
				filename: 'wp-gistpen-admin.min.js',
				dir: 'admin/assets/js/'
			},
		},
		editor: {
			files: ['admin/assets/js/wp-gistpen-editor.js'],
			output: {
				filename: 'wp-gistpen-editor.min.js',
				dir: 'admin/assets/js/'
			},
		},
	},
	scss: {
		admin: {
			files: ['admin/assets/scss/wp-gistpen-admin.scss'],
			output: 'admin/assets/css/'
		}
	},
	add: [
		'**/*.php',
		'**/*.png',
		'**/*.pot',
		'.*',
		'*.txt',
		'*.php',
		'*.json',
		'*.lock',
		'!node_modules/**',
		'!includes/**',
		'!public/assets/vendor/**',
		'!*.sublime-*'],
	build: 'build/'
};

gulp.task( 'init', [ 'install', 'scripts', 'styles' ]);

gulp.task( 'update',[ 'scripts', 'styles' ] );

gulp.task( 'watch', [ 'update' ], function () {
	gulp.watch( paths.js.public.files, [ 'update' ] );
});

gulp.task('build', function() {
	runs('copy', 'init', 'zip', 'clean');
});

gulp.task('clean', function(cb) {
	rimraf(paths.build, cb);
});

gulp.task('zip', function() {
	return gulp.src(paths.build + '**')
		.pipe(zip('wp-gistpen.zip'))
		.pipe(gulp.dest('./'));
});

gulp.task('copy', ['set-build-var'], function() {
	return gulp.src(paths.add)
		.pipe(gulp.dest(paths.build));
});

gulp.task('set-build-var', function(cb) {
	var err;
	building = true;
	cb(err);
});

gulp.task('install', ['clean-installs'], function() {
	var composed, bowered;

	if (building) {
		composed = composer({bin: 'composer', cwd: process.cwd()+'/'+paths.build});
		bowered = bower({cwd: paths.build});
	}
	composed = composer({ bin: 'composer' });
	bowered = bower();

	return merge(composed, bowered);
});

gulp.task('clean-installs', ['clean-bower', 'clean-composer']);

gulp.task('clean-bower', function(cb) {
	rimraf('public/assets/vendor', cb);
});

gulp.task('clean-composer', function(cb) {
	rimraf('includes', cb);
});

gulp.task('scripts', ['install'], function() {
	var stream;

	for(var location in paths.js) {
		stream = gulp.src(paths.js[location].files)
			.pipe(concat(paths.js[location].output.filename))
			.pipe(gulpif(building, uglify()))
			.pipe(gulp.dest(paths.js[location].output.dir))
			.pipe(gulpif(building, gulp.dest(paths.build + paths.js[location].output.dir)));
	}

	return stream;

});

gulp.task('styles', ['install'], function() {
	var stream;

	for(var location in paths.scss) {
		stream = gulp.src(paths.scss[location].files)
			.pipe(sass())
			.pipe(gulp.dest(paths.scss[location].output))
			.pipe(gulpif(building, gulp.dest(paths.build + paths.scss[location].output)));
	}

	return stream;

});
