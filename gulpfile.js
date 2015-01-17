var gulp = require('gulp'),
	composer = require('gulp-composer'),
	bower = require('gulp-bower'),
	glob = require('glob'),
	fs = require('fs'),
	Q = require('Q'),
	path = require('path'),
	merge = require('merge-stream'),
	runs = require('run-sequence'),
	concat = require('gulp-concat'),
	uglify = require('gulp-uglify'),
	minify = require('gulp-minify-css'),
	sass = require('gulp-sass'),
	rimraf = require('rimraf'),
	extrep = require('gulp-ext-replace'),
	zip = require('gulp-zip');

gulp.task('default', ['scripts', 'styles', 'packages', 'watch']);

gulp.task('init', function() {
	runs(
		['clean-bower', 'clean-composer'],
		'install',
		['scripts', 'styles', 'packages'],
		'watch'
	);
});

gulp.task('watch', function () {
	gulp.watch(
		'assets/js/!(ace)/*.js',
		['scripts']);
		gulp.watch(
		'assets/scss/**',
		['styles']);
});

gulp.task('build', function() {
	runs(
		['clean-bower', 'clean-composer'],
		['scripts', 'styles', 'packages'],
		'copy',
		'install-build',
		'zip',
		'clean-build'
	);
});

gulp.task('scripts', function() {
	var promises = [];

	glob.sync('assets/js/!(ace)').forEach(function(filePath) {
		if (fs.statSync(filePath).isDirectory()) {
			var defer = Q.defer();
			var pipeline = gulp.src(filePath + '/**/*.js')
				.pipe(concat(path.basename(filePath) + '.js'))
				.pipe(gulp.dest(path.resolve(filePath, '..')))
				.pipe(uglify())
				.pipe(concat(path.basename(filePath) + '.min.js'))
				.pipe(gulp.dest('assets/js'));
			pipeline.on('end', function() {
				defer.resolve();
			});
			promises.push(defer.promise);
		}
	});

	return Q.all(promises);
});

gulp.task('styles', function() {
	return gulp.src('assets/scss/*.scss')
		.pipe(sass())
		.pipe(gulp.dest('assets/css'))
		.pipe(minify())
		.pipe(extrep('.min.css'))
		.pipe(gulp.dest('assets/css'));
});

gulp.task('packages', ['prism', 'ace', 'ajaxq']);

gulp.task('prism', function() {
	var scripts = gulp.src([
		'bower_components/prism/components/prism-core.js',
		'bower_components/prism/components/prism-markup.js',
		'bower_components/prism/components/prism-css.js',
		'bower_components/prism/components/prism-clike.js',
		'bower_components/prism/components/prism-javascript.js',
		'bower_components/prism/components/prism-php.js',
		'bower_components/prism/components/prism-bash.js',
		'bower_components/prism/components/prism-groovy.js',
		'bower_components/prism/components/prism-java.js',
		'bower_components/prism/components/prism-python.js',
		'bower_components/prism/components/prism-ruby.js',
		'bower_components/prism/components/prism-scala.js',
		'bower_components/prism/components/prism-scss.js',
		'bower_components/prism/components/prism-sql.js',
		// New languages - v0.3.0
		'bower_components/prism/components/prism-c.js',
		'bower_components/prism/components/prism-coffeescript.js',
		'bower_components/prism/components/prism-csharp.js',
		'bower_components/prism/components/prism-go.js',
		'bower_components/prism/components/prism-http.js',
		'bower_components/prism/components/prism-ini.js',
		'bower_components/prism/components/prism-markup.js',
		'bower_components/prism/components/prism-objectivec.js',
		'bower_components/prism/components/prism-swift.js',
		'bower_components/prism/components/prism-twig.js',
		// New languages - v0.5.0
		'bower_components/prism/components/prism-actionscript.js',
		'bower_components/prism/components/prism-applescript.js',
		'bower_components/prism/components/prism-dart.js',
		'bower_components/prism/components/prism-eiffel.js',
		'bower_components/prism/components/prism-erlang.js',
		'bower_components/prism/components/prism-gherkin.js',
		'bower_components/prism/components/prism-git.js',
		'bower_components/prism/components/prism-haml.js',
		// Prism Plugins
		'bower_components/prism/plugins/line-numbers/prism-line-numbers.js',
		'bower_components/prism/plugins/line-highlight/prism-line-highlight.js',
		'bower_components/prism/plugins/file-highlight/prism-file-highlight.js',
	])
		.pipe(concat('prism.js'))
		.pipe(gulp.dest('assets/js'))
		.pipe(uglify())
		.pipe(extrep('.min.js'))
		.pipe(gulp.dest('assets/js'));

	var styles = gulp.src('bower_components/prism/**/*.css')
		.pipe(gulp.dest('assets/css/prism/'));

	return merge(scripts, styles);
});

gulp.task('ace', function() {
	return gulp.src('bower_components/ace-builds/src-min-noconflict/**')
		.pipe(gulp.dest('assets/js/ace'));
});

gulp.task('ajaxq', function() {
	return gulp.src('bower_components/ajaxq/*.js')
		.pipe(concat('ajaxq.js'))
		.pipe(gulp.dest('assets/js'))
		.pipe(uglify())
		.pipe(extrep('.min.js'))
		.pipe(gulp.dest('assets/js'));
});

gulp.task('clean-bower', function(cb) {
	rimraf('bower_components', cb);
});

gulp.task('clean-composer', function(cb) {
	rimraf('lib', cb);
});

gulp.task('install', function() {
	return merge(composer({ bin: 'composer' }), bower());
});

gulp.task('install-build', function() {
	return merge(composer({cwd: './build', bin: 'composer' }), bower());
});

gulp.task('copy', function() {
	return gulp.src([
		'./**',
		'!./*.png',
		'!./.*',
		'!./*.xml',
		'!./*.zip',
		'!./gulpfile.js',
		'!./*.sublime-*',
		'!./node_modules/**',
		'!./node_modules/',
		'!./bower_components/**',
		'!./bower_components/',
		'!./test/**',
		'!./test/',
	], { base: './' })
		.pipe(gulp.dest('build'));
});

gulp.task('zip', function() {
	return gulp.src(['build/**', '!./*.json', '!./*.lock'])
		.pipe(zip('wp-gistpen.zip'))
		.pipe(gulp.dest('./'));
});

gulp.task('clean-build', function(cb) {
	rimraf('build', cb);
});
