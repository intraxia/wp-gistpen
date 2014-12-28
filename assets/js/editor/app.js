window.wpgpEditor = { Views: {}, Models: {} };

jQuery(function($) {
	"use strict";

	var editor = window.wpgpEditor;

	var app = new editor.Main({
		post_id: $('#post_ID').val(),
		form: $('form#post'),
	});

	editor.app = app;
});
