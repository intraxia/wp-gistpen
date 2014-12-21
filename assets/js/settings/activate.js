window.wpgpSettings = {};

jQuery(function($) {
	"use strict";

	var settings = window.wpgpSettings;

	Prism.hooks.add('after-highlight', function(env) {
		var preview = new settings.Preview();

		preview.setClickHandlers();
	});

	var exp = new settings.Export();

	exp.setClickHandlers();
});
