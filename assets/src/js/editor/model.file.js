(function($){
	var editor = window.wpgpEditor;

	var modelfile = Backbone.Model.extend({
		defaults: {
			slug: "",
			code: "",
			ID: null,
			language: ""
		},

		initialize: function() {
			this.view = new editor.Views.File({model: this});
		},

		deleteFile: function() {
			this.collection.remove(this);
		}
	});

	window.wpgpEditor.Models.File = modelfile;
})(jQuery);
