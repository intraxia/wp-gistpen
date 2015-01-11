## Changelog ##

This change log follows the [Keep a Changelog standards][keepachangelog]. Versions follows [Semantic Versioning][semver].

### [Unreleased][unreleased]

#### Added
* MAJOR FEATURE: Gist interoperability
	- Gistpens can be exported to Gist on a case-by-case basis
	- Most Gists can be imported into Gistpen
		+ Unsupported languages get changed to "Plaintext" - sorry!
* New Feature: Revisions and history
* Bad tests for everything :/

#### Changed
* CMB -> CMB2
* Massive reorganization wit namespacing + autoloading
* Unminified scripts enqueued when `WP_SCRIPT_DEBUG` is true
* ACE editor rewritten in Backbone.js
	- Saving and updating all done with AJAX
* Menu icon pen -> code

#### Fixed
* Deleting bug
	- Files were being left behind when Zips were deleted
* Strings are now translatable
* All languages cleaned up and verified working
	- HTML & XML are split again

### [0.4.0] - 2014-10-03

#### Added
* MAJOR FEATURE: Multiple files can be created in a single Gistpen
	- First step towards Gist compatibility
	- The database gets upgraded to account for this, so PLEASE make a backup before you upgrade
* ACE editor

#### Fixed
* Properly escaping content display

### [0.3.1] - 2014-08-03

#### Fixed
* Forgot to minify JavaScripts

### [0.3.0] - 2014-08-03

#### Changed
* Use [PrismJS](http://prismjs.com/) over SyntaxHighlighter

#### Added
* Options page
* Theme switching 
* Line numbers plugin
* Line-highlighting
* Link to lines
* Languages:
	- C
	- Coffeescript
	- C#
	- Go
	- HTTP
	- ini
	- HTML/Markup (XML has been migrated here)
	- Objective-C
	- Swift
	- Twig

#### Removed
* Languages (*If you need any of these languages readded, please open an issue on [GitHub](https://github.com/mAAdhaTTah/WP-Gistpen) to discuss.)
	- AppleScript
	- ActionScript3
	- ColdFusion
	- CPP
	- Delphi
	- Diff
	- Erlang
	- JavaFX
	- Perl
	- Vb

### [0.2.3] - 2014-07-28

#### Fixed
* Uninstall/reinstall language deleting bug

### [0.2.2] - 2014-07-28

#### Fixed
* Fix mis-enqueued scripts (again!)

### [0.2.1] - 2014-07-27

#### Fixed
* Fix mis-enqueued scripts

### [0.2.0] - 2014-07-26

#### Added
* "Insert Gistpen" button in TinyMCE

### Updated
* Gistpen icon
* Code organization
* README
* Build script

### [0.1.2] - 2014-07-17

#### Fixed
* More bugfixes

### [0.1.1] - 2014-07-17

#### Fixed
* Autoloader

#### Changed
* Use defined constant for dir_path

### [0.1.0] - 2014-07-17

#### Added
* Gistpen post type
* Embeddable in posts via shortcode
* Use SyntaxHighlighter to display

[keepachangelog]: http://keepachangelog.com/
[semver]: http://semver.org/
[unreleased]: https://github.com/mAAdhaTTah/WP-Gistpen/tree/develop
[0.4.0]: https://github.com/mAAdhaTTah/WP-Gistpen/tree/0.4.0
[0.3.1]: https://github.com/mAAdhaTTah/WP-Gistpen/tree/0.3.1
[0.3.0]: https://github.com/mAAdhaTTah/WP-Gistpen/tree/0.3.0
[0.2.3]: https://github.com/mAAdhaTTah/WP-Gistpen/tree/0.2.3
[0.2.2]: https://github.com/mAAdhaTTah/WP-Gistpen/tree/0.2.2
[0.2.1]: https://github.com/mAAdhaTTah/WP-Gistpen/tree/0.2.1
[0.2.0]: https://github.com/mAAdhaTTah/WP-Gistpen/tree/0.2.0
[0.1.2]: https://github.com/mAAdhaTTah/WP-Gistpen/tree/0.1.2
[0.1.1]: https://github.com/mAAdhaTTah/WP-Gistpen/tree/0.1.1
[0.1.0]: https://github.com/mAAdhaTTah/WP-Gistpen/tree/0.1.0
