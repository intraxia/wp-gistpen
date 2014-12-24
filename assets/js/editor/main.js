(function($){
	var editor = window.wpgpEditor;

	var main = Backbone.Model.extend({
		defaults: {
			acetheme: 'ambiance'
		},

		initialize: function(atts, opts) {
			this.view = new editor.Views.Main({model: this});

			this.zip = new editor.Models.Zip(this.get('zip'));
			this.files = new editor.Files(this.get('files'));

			this.attachListeners();
		},

		render: function() {
			this.view.render();
			this.view.$el.prepend( this.zip.view.render().el );
			this.view.$el.find('.wpgp-ace-settings').after( this.files.view.render().el );

			return this.view.$el;
		},

		attachListeners: function() {
			this.on({
				'change:acetheme': this.updateThemes,
			});
		},

		updateThemes: function() {
			this.files.view.updateThemes(this.get('acetheme'));
		},

		addFile: function() {
			var file = new editor.Models.File();

			this.files.add(file);
			this.updateThemes();
		}

		// getNonce: function() {
		// 	return $.trim($('#_ajax_wp_gistpen').val());
		// }

	});

	window.wpgpEditor.Main = main;
})(jQuery);
