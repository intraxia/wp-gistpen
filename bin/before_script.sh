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

	# Log in to docker
	echo $DOCKER_PASSWORD | docker login -u $DOCKER_USERNAME --password-stdin

	# Build assets.
	npm run build

	# Connect to WordPress.
	npm run env start

	# Install composer & activate plugin.
	npm run env run composer install -- --no-ansi --no-dev --no-interaction --no-plugins --no-progress --no-scripts --no-suggest --optimize-autoloader
	npm run env run tests-cli plugin activate wp-gistpen
	npm run env run tests-cli theme activate twentytwenty
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
