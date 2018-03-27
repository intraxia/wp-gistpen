# WP-Gistpen #

**Contributors:** JamesDiGioia  
**Donate link:** http://jamesdigioia.com/  
**Tags:** gist, code snippets, codepen  
**Requires at least:** 4.6  
**Tested up to:** 4.9.3  
**Stable tag:** 1.0.0-beta.10  
**License:** GPLv2  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  

## Description ##

A self-hosted alternative to putting your code snippets on Gist.

[![Build Status](https://travis-ci.org/intraxia/wp-gistpen.svg?branch=develop)](https://travis-ci.org/intraxia/wp-gistpen)  [![Greenkeeper badge](https://badges.greenkeeper.io/intraxia/wp-gistpen.svg)](https://greenkeeper.io/) [![Maintainability](https://api.codeclimate.com/v1/badges/0eee18837a01d55dd33e/maintainability)](https://codeclimate.com/github/intraxia/wp-gistpen/maintainability)

You use WordPress because you want control over your writing. Why give Gist or Codepen your code snippets? WP-Gistpen is a self-hosted replacement for your WordPress blog.

Features include:

* Revision saving
* Gist import & export
* PrismJS syntax highlighting
* Prism-based text editor
* oEmbed snippet embedding

## Installation ##

### Using the WordPress Dashboard ###

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'wp-gistpen'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

### Uploading in WordPress Dashboard ###

1. Download `wp-gistpen.zip` from the WordPress plugins repository.
2. Navigate to the 'Add New' in the plugins dashboard
3. Navigate to the 'Upload' area
4. Select `wp-gistpen.zip` from your computer
5. Click 'Install Now'
6. Activate the plugin in the Plugin dashboard

### Using FTP ###

1. Download `wp-gistpen.zip`
2. Extract the `wp-gistpen` directory to your computer
3. Upload the `wp-gistpen` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard

## Frequently Asked Questions ##

### What are the plugin's requirements? ###

Your PHP version should be 5.4+, and you must be running WordPress v4.6+. This is because WP-Gistpen relies on the WP-API infrastructure.

### How do I create an archive page for my Gistpens? ###

Go to the "Pages" screen and create a new, blank page with the name "Gistpens" and click publish. That URL will now display all of your Gistpens. You can link to this page in the menu to direct users to your Gistpens archive page. You can change the name of the page; just make sure the slug of the page is "gistpens".

### How do I create a Gistpen and insert it into the post? ###

To add a new Gistpen, go to Gistpens -> Add New, and add your code. You can enable or disable Gist syncing on a a per-Gistpen basis.

You can also create and insert a Gistpen directly into your post/page from the visual editor by clicking the code button. From the pop-up, search for your gistpen, select it, and click insert. Your shortcode will be inserted into the editor.

### How do I highlight specific lines in my Gistpen? ###

To highlight a specific line, add `highlight=^^`, where ^^ is a line number or range of numbers you want highlighted, like this ([via PrismJS documentation](http://prismjs.com/plugins/line-highlight/)):

* A single number refers to the line with that number
* Ranges are denoted by two numbers, separated with a hyphen (-)
* Multiple line numbers or ranges are separated by commas.
* Whitespace is allowed anywhere and will be stripped off.

Examples:

* `highlight="5"`: The 5th line
* `highlight="1-5"`: Lines 1 through 5
* `highlight="1,4"`: Line 1 and line 4
* `highlight="1-2,5,9-20"`: Lines 1 through 2, line 5, lines 9 through 20

### How do I link to a specific line? ###

You can link to a specific line in your Gistpen by linking to `#gistpen-{gistpen-slug}.{lines}`. The lines don't need to be highlighted in advance for the link to work, and they will be highlighted when the link is clicked. The `{lines}` should match the same format as above.

### How do I get my Gist token from GitHub? ###

1. Login to GitHub.
2. Go to Settings -> Applications.
3. Under "Personal access tokens", click "Generate New Token."
4. Give it a name, click create
	* The default settings work, but make you sure you at least include the `gist` and `user` scopes.
5. Copy the generated token.
6. Paste it into the settings page.
7. ???
8. Profit!

### How do I sync my Gistpens to Gist? ###

Gistpens can be exported en masse. All Gistpens will be synced, only if the Gistpen hasn't been synced yet, but **regardless of whether syncing for the Gistpen is enabled**. Syncing will then be enabled on the exported Gistpens.

If you do not want this, you can enable/disable Gistpen syncing on a per-Gistpen basis. Just click the checkbox on the Gistpen edit page, and any changes will be reflected on the corresponding Gist on update. If you uncheck that box, future changes will not be reflected on Gist.

To go this, go to the Gistpens settings page and click "Jobs". Next to the the "Export Job", click "Dispatch Job". After the Job is done, check out the log messages to ensure everything exported correctly.

### How do I import my Gistpens from Gist? ###

Go to the Gistpents settings page and click "Jobs". Next to the "Import Job", click "Dispatch Job", then check out the log messages to ensure everything was imported correctly.

## Screenshots ##

![](screenshot-1.png)

The Insert Gistpen dialog after you click the pen in the visual editor.

![](screenshot-2.png)

Default theme - How your code will appear in your post.

![](screenshot-3.png)

Twilight theme with line numbers enabled. Check out all the themes at [PrismJS.com](http://prismjs.com).

![](screenshot-4.png)

The current options page – first page.

![](screenshot-5.png)

The current options page – second page.

![](screenshot-6.png)

The current options page – third page.

![](screenshot-7.png)

PrismJS-based text editor.
