(function($){
	var editor = window.wpgpEditor;

	var main = Backbone.Model.extend({
		$nonce: $('#_ajax_wp_gistpen'),
		defaults: {
			acetheme: 'ambiance'
		},

		initialize: function(atts, opts) {
			this.attachListeners();

			this.getData();

			this.view = new editor.Views.Main({model: this});

			this.zip = new editor.Models.Zip(this.get('zip'));
			this.files = new editor.Files(this.get('files'));
		},

		getData: function() {
			that = this;

			$.post(ajaxurl, {
				nonce: that.getNonce(),
				action: 'get_ace_theme'
			}, function(response, textStatus, xhr) {
				if(response.success === true && "" !== response.data.theme) {
					that.set('acetheme', response.data.theme);
				}
			});
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
			that = this;

			this.files.view.updateThemes(this.get('acetheme'));

			$.post(ajaxurl, {
				nonce: that.getNonce(),
				action: 'save_ace_theme',
				theme: that.get('acetheme')
			}, function(response, textStatus, xhr) {
				if(response.success === false) {
					// @todo display error message
				}
			});
		},

		addFile: function() {
			var file = new editor.Models.File();

			this.files.add(file);
			this.updateThemes();
		},

		getNonce: function() {
			return $.trim(this.$nonce.val());
		}

	});

	window.wpgpEditor.Main = main;
})(jQuery);
