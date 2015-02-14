# How to Contribute to WP-Gistpen

## Requirements

For development, WP-Gistpen requires these tools:

1. PHP 5.3+
	* Note: WordPress is compatible back to 5.2, so not all users will be able to use this plugin yet.
2. [Composer][1], for back-end libraries.
3. [npm][2], for build tools.
3. [Bower][3], for front-end libraries.
4. [Gulp][4], for project builds

If you need help with any of these tools, please ask!

## Installation

1. Clone the repository.
2. `npm install`
3. `gulp init`
4. Make and commit your changes
5. Open a pull request again `develop`
4. ???
5. Profit!

## Guidelines

#### Code style

WP-Gistpen adheres to the WordPress coding standards, which are enforced by [Scrutinizer][6]. When you open a pull request, please address any issues it raises for you, if possible.

### Unit tests

If you're writing new functions or features, please include unit tests for them. Unit tests should test as few assumptions as possible, which means minimize the number of assertions per test, if possible. Unit test function names start with `test_should` followed by what the test should prove.

The current unit tests need to be rewritten and improved, so most of them don't adhere to this standard. Please help us improve by adhering to these standards in your pull requests.

### Supporting a New Language

Currently, our language support is limited primarily by the languages supported by Prism. If you want to add support to WP-Gistpen for a language that is supported by Prism, please confirm that it works in these places:

1. Prism highlighting
2. ACE Editor
3. Gist Import/Export

This way we can maintain consistent support for all languages.

If Prism doesn't support the language you are looking for, please open a ticket and we'll look into adding support.

  [1]: https://getcomposer.org/
  [2]: https://www.npmjs.org/
  [3]: http://bower.io/
  [4]: http://gulpjs.com/
  [5]: https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate
  [6]: https://scrutinizer-ci.com/
