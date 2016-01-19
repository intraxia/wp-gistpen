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

gulp.task('default', ['scripts', 'styles', 'packages', 'watch']);

gulp.task('watch', function () {
    gulp.watch('src/js/**/*.js', ['scripts']);
    gulp.watch('src/scss/**/*.scss', ['styles']);
});

gulp.task('build', ['scripts', 'styles', 'packages']);

gulp.task('scripts',function () {
    var promises = [];

    ['post', 'tinymce', 'settings'].forEach(function (file) {
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
    return gulp.src('src/scss/*.scss')
        .pipe(sass())
        .pipe(gulp.dest('assets/css'))
        .pipe(minify())
        .pipe(extrep('.min.css'))
        .pipe(gulp.dest('assets/css'));
});

gulp.task('packages', ['prism', 'ace']);

gulp.task('prism', function() {
	var promises = [];

	var scriptspromise = Q.defer();
	var scriptspipe = gulp.src([
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
		'bower_components/prism/components/prism-handlebars.js',
		'bower_components/prism/components/prism-jade.js',
		'bower_components/prism/components/prism-latex.js',
		'bower_components/prism/components/prism-less.js',
		'bower_components/prism/components/prism-markdown.js',
		'bower_components/prism/components/prism-matlab.js',
		'bower_components/prism/components/prism-nasm.js',
		'bower_components/prism/components/prism-perl.js',
		'bower_components/prism/components/prism-powershell.js',
		'bower_components/prism/components/prism-r.js',
		'bower_components/prism/components/prism-rust.js',
		'bower_components/prism/components/prism-scheme.js',
		'bower_components/prism/components/prism-smarty.js',
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
	scriptspipe.on('end', function() {
		scriptspromise.resolve();
	});
	promises.push(scriptspromise);

	var stylespromise = Q.defer();
	var stylespipe = gulp.src('bower_components/prism/**/*.css')
		.pipe(gulp.dest('assets/css/prism/'));
	stylespipe.on('end', function() {
		scriptspromise.resolve();
	});

	promises.push(stylespromise);

	return promises;
});

gulp.task('ace', function() {
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
