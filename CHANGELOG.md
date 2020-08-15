## Changelog ##

This change log follows the [Keep a Changelog standards](http://keepachangelog.com/). Versions follows [Semantic Versioning](http://semver.org/).

### [2.0.0-alpha.0][2.0.0-alpha.0]

#### Added ####

* Gutenberg block

### [1.2.1][1.2.1]

#### Fixed####

* Fixed rendering output with JSX language

### [1.2.0][1.2.0] ###

**Note: This bumps the minimum required WordPress version to 4.7. A future release will bump the minimum WordPress version to 5.2 and the minimum PHP version to 5.6. This will be released as 2.0. There is plenty of time, but this is coming.**

#### Added ####

* Added slug to repo API
* Add copy shortcode button to editor
* Add API pagination to Repo resource
* Bump prism-themes and add new themes

#### Fixed ####

* Updated JS dependencies
* Fix button alignment on editor controls

### [1.1.6][1.1.6] ###

#### Fixed ####

* Busted deploy

### [1.1.5][1.1.5] ###

#### Fixed ####

* Add PHP version requirement
	* This was previously handled in code only, but will now appear in wp.org
* Render BE error messages in UI
* Remove JSS for smaller bundle

### [1.1.4][1.1.4] ###

#### Fixed ####

* Added label to filename input
* Bumped dependencies


### [1.1.3][1.1.3] ###

#### Fixed ####

* Escape filename on FE & BE
* Accessibility improvements
	* Add line numbers to the editor
	* Allow alt + tab to escape the editor
	* Improve border of focused controls
	* Fix toolbar colors

### [1.1.2][] - 2019-05-27 ###

* Rewrite & stabilize FE into TypeScript & latest brookjs

### [1.1.1][] - 2018-06-28 ###

* Fixed buggy deploy

### [1.1.0][] - 2018-06-22 ###

* Upgrade the editor into React
* Remove LightNCandy
	* First step towards supporting php-5.3 again
* Tighten up Flowtypes
* Display spinner when saving

### [1.0.3][] - 2018-06-09 ###

* More editor CSS fixes
* Generate languages.json instead of maintaining manually
* Validate blobs in API endpoint
	- Requires filename now
* Order Runs in descending order
* Fix demo code indentation
* Simplify Blob component implementation

### [1.0.2][] - 2018-04-03 ###

* Fix CSS display in editor

### [1.0.1][] - 2018-04-03 ###

* Fix deployment scripts
	* The old scripts resulted in a botched deployment with missing files. This readds them.

### [1.0.0][] - 2018-03-26 ###

Almost three years in the making!

#### Removed ####
* Database migration from old versions
	* If you're on the latest version of WP-Gistpen, you should have no problems. Given that the plugin hasn't been updated on .org in 2+ years, and .org reports the only versions are 0.5.x+, anyone who is still using an old version of WP-Gistpen is unlikely to upgrade to 1.0.0+ anyway. We are going to remove the Migration code because it'll allow us to remove a lot of old code that is currently only being used by it.

		If you're using an old version of WP-Gistpen, you'll need to upgrade to 0.5.2+ before upgrading to 1.0.0 or the database migration will not be performed correctly. Fresh installs should have no problems either.

#### Added ####
* **Completely rewritten from the ground up**
* New internal architecture
	* Built on [Jaxion][], WordPress framework
	* Improved Database layer & use of Models
* WP-API integration
	* This requires an upgrade to WordPress 4.6+.
* New code snippet editor
	* Custom snippet editor
	* Built on [brookjs][], Prism, & React.js
* New Prism plugins:
	* Show invisibles: Display tabs and line returns as characters
	* Show language: Display the language in the embed
	* Copy-to-clipboard: Display button to copy code to clipboard
* Supports all Prism languages & built-in Prism themes + bonus from prism-themes

#### Fixed ####
* Fixed code sample display on mobile
* Fixed display on custom post type pages
* Fixed bug where adding two files would only save one at a time
* Tighten up revision saving logic

### [0.5.8] - 2015-07-26 ###

#### Fixed ####
* Fixed a bug introduced in WordPress 4.2.3 where cap checks fail for `edit_post` on a post_id of 0.
* Also loosened a couple checks because null values were being cast to 0.

### [0.5.7] - 2015-05-23 ###

#### Fixed ####
* Use `wpdb` to get the posts table for alternate prefix and Multisite compatibility (thanks @janizde!)

### [0.5.6] - 2015-02-17 ###

#### Fixed ####
* Logic bugs raised by Travis

### [0.5.5] - 2015-02-15 ###

#### Fixed ####
* Fixed syncing bug, renabled everything
	- So sorry about the multiple releases. Ran into the problem after deploying, and didn't want anyone's DB to get out-of-sync. Everything looks good now, but let me know if you experience issues.

### [0.5.4] - 2015-02-14 ###

#### Fixed ####
* Disable importing/exporting en masse until we fix export/sync bug

### [0.5.2] - 2015-02-14 ###

#### Fixed ####
* Add new database migration to fix Gist exports of pre-0.5.0 Gistpens

### [0.5.0] - 2015-02-14 ###

#### Added ####
* MAJOR FEATURE: Gist interoperability
	- Gistpens can be exported to Gist on a case-by-case basis
	- Most Gists can be imported into Gistpen
		+ Unsupported languages get changed to "Plaintext" - sorry!
* New Feature: Revisions and history
* Bad tests for everything :/
* New languages:
	- Actionscript
	- Applescript
	- Dart
	- Eiffel
	- Erlang
	- Gherkin
	- Git
	- Haml
	- Handlebars
	- Jade
	- LaTeX
	- LESS
	- Markdown
	- Matlab
	- NASM
	- Perl
	- Powershell
	- R
	- Rust
	- Scheme
	- Smarty

#### Changed ####
* CMB -> CMB2
* Massive reorganization wit namespacing + autoloading
* Unminified scripts enqueued when `WP_SCRIPT_DEBUG` is true
* ACE editor rewritten in Backbone.js
	- Saving and updating all done with AJAX
* Menu icon pen -> code
* Improved .org deployment process (No more dumbass "forgot to minify js" commits/releases)

#### Fixed ####
* Deleting bug
	- Files were being left behind when Zips were deleted
* Strings are now translatable
* All languages cleaned up and verified working
	- HTML & XML are split again

### [0.4.0] - 2014-10-03 ###

#### Added ####
* MAJOR FEATURE: Multiple files can be created in a single Gistpen
	- First step towards Gist compatibility
	- The database gets upgraded to account for this, so PLEASE make a backup before you upgrade
* ACE editor

#### Fixed ####
* Properly escaping content display

### [0.3.1] - 2014-08-03 ###

#### Fixed ####
* Forgot to minify JavaScripts

### [0.3.0] - 2014-08-03 ###

#### Changed ####
* Use [PrismJS](http://prismjs.com/) over SyntaxHighlighter

#### Added ####
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

#### Removed ####
* Languages (*If you need any of these languages readded, please open an issue on [GitHub](https://github.com/intraxia/WP-Gistpen) to discuss.)
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

### [0.2.3] - 2014-07-28 ###

#### Fixed ####
* Uninstall/reinstall language deleting bug

### [0.2.2] - 2014-07-28 ###

#### Fixed ####
* Fix mis-enqueued scripts (again!)

### [0.2.1] - 2014-07-27 ###

#### Fixed ####
* Fix mis-enqueued scripts

### [0.2.0] - 2014-07-26 ###

#### Added ####
* "Insert Gistpen" button in TinyMCE

#### Updated ####
* Gistpen icon
* Code organization
* README
* Build script

### [0.1.2] - 2014-07-17 ###

#### Fixed ####
* More bugfixes

### [0.1.1] - 2014-07-17 ###

#### Fixed ####
* Autoloader

#### Changed ####
* Use defined constant for dir_path

### [0.1.0] - 2014-07-17 ###

#### Added ####
* Gistpen post type
* Embeddable in posts via shortcode
* Use SyntaxHighlighter to display

[Jaxion]: https://github.com/intraxia/jaxion
[brookjs]: https://github.com/valtech-nyc/brookjs
[unreleased]: https://github.com/intraxia/WP-Gistpen/tree/develop
[1.2.1]: https://github.com/intraxia/WP-Gistpen/tree/1.2.1
[1.2.0]: https://github.com/intraxia/WP-Gistpen/tree/1.2.0
[1.1.6]: https://github.com/intraxia/WP-Gistpen/tree/1.1.6
[1.1.5]: https://github.com/intraxia/WP-Gistpen/tree/1.1.5
[1.1.4]: https://github.com/intraxia/WP-Gistpen/tree/1.1.4
[1.1.3]: https://github.com/intraxia/WP-Gistpen/tree/1.1.3
[1.1.2]: https://github.com/intraxia/WP-Gistpen/tree/1.1.2
[1.1.1]: https://github.com/intraxia/WP-Gistpen/tree/1.1.1
[1.1.0]: https://github.com/intraxia/WP-Gistpen/tree/1.1.0
[1.0.3]: https://github.com/intraxia/WP-Gistpen/tree/1.0.3
[1.0.2]: https://github.com/intraxia/WP-Gistpen/tree/1.0.2
[1.0.1]: https://github.com/intraxia/WP-Gistpen/tree/1.0.1
[1.0.0]: https://github.com/intraxia/WP-Gistpen/tree/1.0.0
[0.5.8]: https://github.com/intraxia/WP-Gistpen/tree/0.5.8
[0.5.7]: https://github.com/intraxia/WP-Gistpen/tree/0.5.7
[0.5.6]: https://github.com/intraxia/WP-Gistpen/tree/0.5.6
[0.5.5]: https://github.com/intraxia/WP-Gistpen/tree/0.5.5
[0.5.4]: https://github.com/intraxia/WP-Gistpen/tree/0.5.4
[0.5.2]: https://github.com/intraxia/WP-Gistpen/tree/0.5.2
[0.5.0]: https://github.com/intraxia/WP-Gistpen/tree/0.5.0
[0.4.0]: https://github.com/intraxia/WP-Gistpen/tree/0.4.0
[0.3.1]: https://github.com/intraxia/WP-Gistpen/tree/0.3.1
[0.3.0]: https://github.com/intraxia/WP-Gistpen/tree/0.3.0
[0.2.3]: https://github.com/intraxia/WP-Gistpen/tree/0.2.3
[0.2.2]: https://github.com/intraxia/WP-Gistpen/tree/0.2.2
[0.2.1]: https://github.com/intraxia/WP-Gistpen/tree/0.2.1
[0.2.0]: https://github.com/intraxia/WP-Gistpen/tree/0.2.0
[0.1.2]: https://github.com/intraxia/WP-Gistpen/tree/0.1.2
[0.1.1]: https://github.com/intraxia/WP-Gistpen/tree/0.1.1
[0.1.0]: https://github.com/intraxia/WP-Gistpen/tree/0.1.0
