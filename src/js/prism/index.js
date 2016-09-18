/**
 * Load our dependencies.
 */
var extend = require('lodash.assign');

/**
 * Import Prism and its autoloader.
 */
var Prism = require('prismjs/components/prism-core');
require('prismjs/plugins/autoloader/prism-autoloader');
// Prism highlights automatically by default.
document.removeEventListener('DOMContentLoaded', Prism.highlightAll);

/**
 * Register the toolbar and its buttons.
 */
var toolbar = require('./toolbar');

var edit = require('./edit');
toolbar.registerButton(edit.button);

var clipboard = require('./clipboard');
toolbar.registerButton(clipboard.button);

var filename = require('./filename');
toolbar.registerButton(filename.button);

/**
 * Register the toolbar with Prism.
 */
Prism.hooks.add('complete', toolbar.hook);

var api = {};

/**
 * Add our extensions.
 */
var themeloader = require('./themeloader');
var pluginloader = require('./pluginloader');

extend(api, Prism, themeloader, pluginloader);

/**
 * Exports!
 */
module.exports = api;
