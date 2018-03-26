# How to Contribute to WP-Gistpen

## Requirements

For development, WP-Gistpen requires these tools:

1. PHP 5.4+
	* Note: WordPress is compatible back to 5.2, so not all users will be able to use this plugin.
2. [Composer][composer], for back-end libraries.
3. [node][node], for build tools.

If you need help with any of these tools, please ask! We all had to learn at some point, and we're happy to help.

## Installation

1. Clone the repository.
1. `npm install && composer install && npm run build`.
1. Make and commit your changes. Don't forget to update the tests!
1. Open a pull request against `develop`.
1. We'll review the pull request and help you through any changes.
1. If everything passes, we'll merge the

## Development Scripts

### Front-end

* `npm run dev:app` - Starts a Webpack watcher to rebuild the assets as you modify them.
* `npm run dev:tdd` - Starts a Mocha watcher to rerun your tests as you modify them.
* `npm run dev:storybook` - Starts a Storybook development environment.
* `npm test` - Run all the tests, including linting, type checking, and unit testing.

### Back-end

* `composer test` - Run the unit tests
	* **NOTE:** Before running the tests, run `bash bin/install-wp-tests.sh wordpress_test <username> <password>` to set up the support files and database. In a standard homebrew install on OS X, `root` & `password` worked for the local databases. Your mileage may vary.

## Guidelines

### Code style

WP-Gistpen adheres to the WordPress coding standards on the back-end, which are enforced by [Scrutinizer][6] & [CodeClimate][7]. When you open a pull request, please address any issues it raises for you, if possible. The front-end code style is enforced by [ESLint][8] and uses [Valtech's code style][9].

### Unit tests

If you're writing new functions or features, please include unit tests for them. Unit tests should test as few assumptions as possible, which means minimize the number of assertions per test. On the back-end, unit test function names start with `test_should` followed by what the test should prove.

On the front-end, unit tests are in files with `*.spec.js` as the extension or stories for front-end components with `*.story.js` as the extension. Stories describe how a component looks in various states, and unit tests describe how a component behaves and changes over time.

  [composer]: https://getcomposer.org/
  [node]: https://nodejs.org/en/
  [4]: http://gulpjs.com/
  [5]: https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate
  [6]: https://scrutinizer-ci.com/
  [7]: https://codeclimate.com/github/intraxia/wp-gistpen
  [8]: https://eslint.org/
  [9]: https://github.com/valtech-nyc/eslint-config-valtech
