#!/usr/bin/env bash
set -ex

if [[ $FRONT_END == 'true' ]]; then
	npm run build
elif [[ $E2E == 'true' ]]; then
	# Upgrade docker-compose.
	sudo rm /usr/local/bin/docker-compose
	curl -sL https://github.com/docker/compose/releases/download/1.24.0/docker-compose-`uname -s`-`uname -m` > docker-compose
	chmod +x docker-compose
	sudo mv docker-compose /usr/local/bin

	# Download and unpack WordPress.
	curl -sL https://wordpress.org/nightly-builds/wordpress-latest.zip -o /tmp/wordpress-latest.zip
	unzip -q /tmp/wordpress-latest.zip -d /tmp
	mkdir -p wordpress/src
	mv /tmp/wordpress/* wordpress/src

	# Create the upload directory with permissions that Travis can handle.
	mkdir -p wordpress/src/wp-content/uploads
	chmod 767 wordpress/src/wp-content/uploads

	# Grab the tools we need for WordPress' local-env.
	curl -sL https://github.com/WordPress/wordpress-develop/archive/master.zip -o /tmp/wordpress-develop.zip
	unzip -q /tmp/wordpress-develop.zip -d /tmp
	mv \
		/tmp/wordpress-develop-master/tools \
		/tmp/wordpress-develop-master/tests \
		/tmp/wordpress-develop-master/.env \
		/tmp/wordpress-develop-master/docker-compose.yml \
		/tmp/wordpress-develop-master/wp-cli.yml \
		/tmp/wordpress-develop-master/*config-sample.php \
		/tmp/wordpress-develop-master/package.json wordpress

	touch wordpress/src/wp-config.php
	chmod 767 wordpress/src/wp-config.php

	# Install WordPress.
	cd wordpress
	npm install dotenv wait-on
	npm run env:start
	sleep 20
	npm run env:install
	cd ..

	# Build assets.
	npm run build

	# Connect to WordPress.
	npm run wpenv connect

	# Install composer & activate plugin.
	npm run wpenv docker-run -- php composer install --no-ansi --no-dev --no-interaction --no-plugins --no-progress --no-scripts --no-suggest --optimize-autoloader
	npm run wpenv cli plugin activate wp-gistpen
else
	# If it's not this specific version. Currently disabled.
	if [[ !($TRAVIS_PHP_VERSION == '5.6' && $WP_VERSION == 'disabled' && $WP_MULTISITE == '0') ]]; then
		# Remove xdebug (makes the build slow).
		phpenv config-rm xdebug.ini;
	else
		# We're generating code coverage, so download the reporter.
		curl -L https://codeclimate.com/downloads/test-reporter/test-reporter-latest-linux-amd64 > ./cc-test-reporter
		chmod +x ./cc-test-reporter
		./cc-test-reporter before-build
	fi

	composer self-update
	composer install
	bash bin/install-wp-tests.sh wordpress_test root '' localhost $WP_VERSION
	curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
	chmod +x wp-cli.phar
	mkdir $PWD/.bin
	mv wp-cli.phar $PWD/.bin/wp
	export PATH=$PATH:$PWD/.bin/
	wp --version
fi
