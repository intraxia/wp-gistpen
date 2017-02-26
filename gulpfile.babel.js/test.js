const gulp = require('gulp');
const gutil = require('gulp-util');
const { Server } = require('karma');
const childProcess = require('child_process');
const flowBin = require('flow-bin');
const reporter = require('flow-reporter');

gulp.task('test', ['test:unit', 'test:typecheck']);

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

gulp.task('test:typecheck', done => {
    let result = '';
    const stream = childProcess.spawn(flowBin, [
        'status',
        '--json'
    ]);

    stream.stdout.on('data', data => {
        result += data.toString();
    });

    stream.stdout.on('end', () =>{
        result = JSON.parse(result);

        if (result.errors.length) {
            reporter(result.errors);

            done(new gutil.PluginError('gulp-flow', 'Flow failed'));
        } else {
            done();
        }
    });
});
