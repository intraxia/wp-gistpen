#!/usr/bin/env bash

if [[ $FRONT_END == 'true' ]]; then
	npm install -g greenkeeper-lockfile@1
	greenkeeper-lockfile-update
	# Disable build during TS conversion
	npm run build
else
	# If it's not nightly
	if [[ ($TRAVIS_PHP_VERSION != 'nightly') ||
		# or it's not this specific version.
		!($TRAVIS_PHP_VERSION == '5.4' && $WP_VERSION == 'latest' && $WP_MULTISITE == '0') ]]; then
		phpenv config-rm xdebug.ini;
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
