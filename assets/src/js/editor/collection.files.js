(function($){
	var editor = window.wpgpEditor;

	var files = Backbone.Collection.extend({
		model: function(attrs, options) {
			return new editor.Models.File(attrs, options);
		},

		initialize: function() {
			this.view = new editor.Views.Files({collection: this});
			this.on({
				'add': this.addFile
			});
		},

		addFile: function() {
			this.view.render();
		}
	});

	window.wpgpEditor.Files = files;
})(jQuery);
