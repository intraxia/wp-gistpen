var Prism = require('./compiled');
var extend = require('lodash.assign');







var api = {};

/**
 * Add our extensions.
 */
var themeloader = require('./themeloader');
var pluginloader = require('./pluginloader');

extend(api, Prism, themeloader, pluginloader);

module.exports = api;
