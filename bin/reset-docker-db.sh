#!/usr/bin/env bash

docker-compose exec db mysql -uwordpress -pdbpass -e 'DROP database wordpress; CREATE database wordpress;'
docker-compose exec app wp core install --allow-root \
	--url="http://localhost:3000" \
	--title="wp-gistpen test site" \
	--admin_user="admin" \
	--admin_email="admin@example.test"\
	--admin_password="wpgppass1";
docker-compose exec app wp plugin activate wp-gistpen --allow-root
docker-compose exec app wp plugin delete akismet --allow-root
docker-compose exec app wp plugin delete hello.php --allow-root
docker-compose exec app wp plugin install classic-editor --allow-root
docker-compose exec app wp rewrite structure '/%postname%/' --allow-root
