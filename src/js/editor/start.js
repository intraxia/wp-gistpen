var MainModel = require('./main/model');
var $ = window.jQuery;
window.wpgp = window.wpgp || {};

$(document).ready(function($) {
	window.wpgp.app = new MainModel({
		post_id: $('#post_ID').val(),
		form: $('form#post')
	});
});
