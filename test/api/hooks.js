const hooks = require('hooks');
const shell = require('shelljs');

hooks.beforeAll((t, done) => {
    // @todo check then run
    // shell.exec('wp plugin install https://github.com/WP-API/Basic-Auth/archive/master.zip --path=/tmp/wordpress');
    done();
});

hooks.beforeEach((t, done) => {
    shell.exec('wp db reset --yes --path=/tmp/wordpress');
    shell.exec('wp core install --url="localhost:8080" --title="GistpenTest" --admin_user="admin" --admin_password="password" --admin_email="admin@example.com" --path=/tmp/wordpress');
    shell.exec('wp plugin activate Basic-Auth --path=/tmp/wordpress');
    shell.exec('wp plugin activate wp-gistpen --path=/tmp/wordpress');
    shell.exec('wp rewrite structure /%post%/ --path=/tmp/wordpress');
    shell.exec('wp rewrite flush --path=/tmp/wordpress');

    done();
});

hooks.after('Repo Collection > List Repo Resources', (t, done) => {
    // hooks.log(t.real.body);

    done();
});
