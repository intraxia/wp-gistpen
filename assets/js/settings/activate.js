var Gistpen = {};

jQuery(function($) {
	"use strict";

	Prism.hooks.add('after-highlight', function(env) {
		var preview = new Gistpen.Preview();

		preview.setClickHandlers();
	});

	var exporter = new Gistpen.Export();

	exporter.setClickHandlers();

});
