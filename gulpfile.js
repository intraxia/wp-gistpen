var gulp = require('gulp'),
	rimraf = require('rimraf'),
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
				'public/assets/vendor/prism/components/prism-core.js',
				'public/assets/vendor/prism/components/prism-markup.js',
				'public/assets/vendor/prism/components/prism-css.js',
				'public/assets/vendor/prism/components/prism-clike.js',
				'public/assets/vendor/prism/components/prism-javascript.js',
				'public/assets/vendor/prism/components/prism-php.js',
				'public/assets/vendor/prism/components/prism-bash.js',
				'public/assets/vendor/prism/components/prism-groovy.js',
				'public/assets/vendor/prism/components/prism-java.js',
				'public/assets/vendor/prism/components/prism-python.js',
				'public/assets/vendor/prism/components/prism-ruby.js',
				'public/assets/vendor/prism/components/prism-scala.js',
				'public/assets/vendor/prism/components/prism-scss.js',
				'public/assets/vendor/prism/components/prism-sql.js',
				// New languages
				'public/assets/vendor/prism/components/prism-c.js',
				'public/assets/vendor/prism/components/prism-coffeescript.js',
				'public/assets/vendor/prism/components/prism-csharp.js',
				'public/assets/vendor/prism/components/prism-go.js',
				'public/assets/vendor/prism/components/prism-http.js',
				'public/assets/vendor/prism/components/prism-ini.js',
				'public/assets/vendor/prism/components/prism-markup.js',
				'public/assets/vendor/prism/components/prism-objectivec.js',
				'public/assets/vendor/prism/components/prism-swift.js',
				'public/assets/vendor/prism/components/prism-twig.js',
				// Prism Plugins
				'public/assets/vendor/prism/plugins/line-numbers/prism-line-numbers.js',
				'public/assets/vendor/prism/plugins/line-highlight/prism-line-highlight.js',
				'public/assets/vendor/prism/plugins/file-highlight/prism-file-highlight.js',
				// Other files
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
		tinymce: {
			files: ['admin/assets/js/wp-gistpen-tinymce-plugin.js'],
			output: {
				filename: 'wp-gistpen-tinymce-plugin.min.js',
				dir: 'admin/assets/js/'
			},
		},
		editor: {
			files: [
				'admin/assets/js/gistpen-editor.js',
				'admin/assets/js/file-editor.js'
			],
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
		},
		public: {
			files: ['public/assets/scss/wp-gistpen-public.scss'],
			output: 'public/assets/css/'
		},
		editor: {
			files: ['admin/assets/scss/wp-gistpen-editor.scss'],
			output: 'admin/assets/css/'
		}
	},
	copy: [
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

gulp.task('init', function() {
	runs(
		['clean-bower', 'clean-composer'],
		'install',
		['scripts', 'styles']
	);
});

gulp.task('update', ['scripts', 'styles']);

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
	return gulp.src(paths.copy)
		.pipe(gulp.dest(paths.build));
});

gulp.task('set-build-var', function(cb) {
	var err;
	building = true;
	cb(err);
});

gulp.task('install', function() {
	var composed, bowered;

	if (building) {
		composed = composer({bin: 'composer', cwd: process.cwd()+'/'+paths.build});
		bowered = bower({cwd: paths.build});
	}

	composed = composer({ bin: 'composer' });
	bowered = bower();

	return merge(composed, bowered);
});

gulp.task('clean-bower', function(cb) {
	rimraf('public/assets/vendor', cb);
});

gulp.task('clean-composer', function(cb) {
	rimraf('includes', cb);
});

gulp.task('scripts', function() {
	var stream;
	var aceStream;

	for(var location in paths.js) {
		stream = gulp.src(paths.js[location].files)
			.pipe(concat(paths.js[location].output.filename))
			.pipe(gulpif(building, uglify()))
			.pipe(gulp.dest(paths.js[location].output.dir))
			.pipe(gulpif(building, gulp.dest(paths.build + paths.js[location].output.dir)));
	}

	aceStream = gulp.src('public/assets/vendor/ace-builds/src-min-noconflict/**')
	.pipe(gulp.dest('admin/assets/js/ace/'))
	.pipe(gulpif(building, gulp.dest(paths.build + 'admin/assets/js/ace/')));

	return merge(stream, aceStream);

});

gulp.task('styles', function() {
	var stream;

	for(var location in paths.scss) {
		stream = gulp.src(paths.scss[location].files)
			.pipe(sass())
			.pipe(gulp.dest(paths.scss[location].output))
			.pipe(gulpif(building, gulp.dest(paths.build + paths.scss[location].output)));
	}

	prismStream = gulp.src('public/assets/vendor/prism/**/*.css')
	.pipe(gulp.dest('public/assets/css/prism/'))
	.pipe(gulpif(building, gulp.dest(paths.build + 'public/assets/css/prism/')));

	return merge(stream, prismStream);

});
