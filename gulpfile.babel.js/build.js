const gulp = require('gulp');
const shell = require('shelljs');
const fs = require('fs');

gulp.task('build', ['build:zip']);

gulp.task('build:zip', () => {
    const ZIP_DIR = fs.mkdtempSync('/tmp/wp-gistpen-');
    const SRC_DIR = shell.pwd().stdout;

    shell.echo(`Building in tmp dir: ${ZIP_DIR}`);

    shell.echo('Checking out index...');
    shell.exec(`git checkout-index --quiet --all --force --prefix=${ZIP_DIR}/`);
    shell.echo('Done!');

    shell.echo('Installing dependencies...');
    shell.cd(ZIP_DIR);
    shell.exec('npm i');
    shell.exec('composer install --quiet --no-dev --optimize-autoloader &>/dev/null');
    shell.echo('Done!');

    shell.echo('Building assets...');
    shell.exec('gulp prod');
    shell.echo('Done!');

    shell.echo('Converting the README to WordPress format...');
    shell.exec(`${ZIP_DIR}/bin/wp2md ${ZIP_DIR}/README.md ${ZIP_DIR}/README.txt to-wp`);
    shell.echo('Done!');

    shell.echo('Removing unwanted development files using .svnignore...');
    shell.rm('-rf', shell.cat(`${ZIP_DIR}/.svnignore`).stdout.split('\n'));
    shell.echo('Done!');

    shell.echo('Building production release zip...');
    shell.exec('zip -r wp-gistpen * --quiet');
    shell.mv('wp-gistpen.zip', `${SRC_DIR}/wp-gistpen.zip`);
    shell.echo('Done!');
});
