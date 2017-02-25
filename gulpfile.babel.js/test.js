const gulp = require('gulp');
const gutil = require('gulp-util');
const { Server } = require('karma');

gulp.task('test', ['test:unit']);

gulp.task('test:unit', done => {
    const server = new Server({
        configFile: __dirname + '/karma.conf.js',
        singleRun: true
    }, () => {
        gutil.log('Tests complete');

        done();
    });

    server.start();
});
