var Preview = require('./preview');
var Export = require('./export');
var Import = require('./import');

window.jQuery(function() {
	Prism.hooks.add('after-highlight', function(env) {
		Preview.init();
	});

	Export.init();
	Import.init();
});
