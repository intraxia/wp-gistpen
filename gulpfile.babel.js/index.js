const gulp = require('gulp');

require('./build');
require('./common');
require('./dev');
require('./scripts');

gulp.task('prod', ['scripts', 'common']);
gulp.task('default', ['scripts:dev', 'common']);
