var Preview = require('./preview');
var Export = require('./export');
var Import = require('./import');

jQuery(function($) {
	"use strict";

	var settings = window.wpgpSettings;

	Prism.hooks.add('after-highlight', function(env) {
		var preview = new Preview();

		preview.setClickHandlers();
	});

	var exp = new Export();

	exp.setClickHandlers();

	var imp = new Import();

	imp.setClickHandlers();
});
