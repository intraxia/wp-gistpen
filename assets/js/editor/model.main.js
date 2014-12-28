(function($){
	var editor = window.wpgpEditor;

	var main = Backbone.Model.extend({
		$nonce: $('#_ajax_wp_gistpen'),
		defaults: {
			acetheme: 'ambiance'
		},

		initialize: function(atts, opts) {
			this.getData();

			this.view = new editor.Views.Main({model: this});
		},

		attachListeners: function() {
			this.on({
				'change:acetheme': this.updateThemes,
			});
		},

		updateThemes: function() {
			var that = this;

			this.files.view.updateThemes(this.get('acetheme'));

			$.post(ajaxurl, {
				nonce: that.getNonce(),
				action: 'save_ace_theme',
				theme: that.get('acetheme')
			}, function(response, textStatus, xhr) {
				if(response.success === false) {
					that.view.displayMessage(response.data.code, response.data.message);
				}
			});
		},

		getData: function() {
			var that = this;

			$.ajaxq('getData', {
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'get_gistpen',
					nonce: that.getNonce(),

					post_id: that.get('post_id'),
				},
			})
			.done(function(response) {
				if(response.success === false) {
					that.view.displayMessage(response.data.code, response.data.message);
				} else {
					that.set('files', response.data.files);
					delete response.data.files;

					that.set('zip', response.data);

					that.zip = new editor.Models.Zip(that.get('zip'));
					that.files = new editor.Files(that.get('files'));

					that.get('form').prepend(that.render());
				}
			});

			$.ajaxq('getData', {
				url: ajaxurl,
				type: 'POST',
				data: {
					nonce: that.getNonce(),
					action: 'get_ace_theme'
				},
			})
			.done(function(response) {
				if(response.success === true && "" !== response.data.theme) {
					that.set('acetheme', response.data.theme);
					that.view.$select.val(that.get('acetheme'));
				}

				that.files.view.updateThemes(that.get('acetheme'));

				that.attachListeners();
			});

		},

		addFile: function() {
			var file = new editor.Models.File();

			this.files.add(file);
			this.files.view.updateThemes(this.get('acetheme'));
		},

		updateGistpen: function() {
			var that = this;

			return $.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'save_gistpen',
					nonce: that.getNonce(),

					zip: that.toJSON()
				},
			});
		},

		toJSON: function() {
			var atts = _.clone( this.zip.attributes );
			atts.files = _.clone( this.files.toJSON() );

			return atts;
		},

		getNonce: function() {
			return $.trim(this.$nonce.val());
		},

		render: function() {
			this.view.render();
			this.view.$el.prepend( this.zip.view.render().el );
			this.view.$el.find('.wpgp-main-settings').after( this.files.view.render().el );

			return this.view.$el;
		},

	});

	window.wpgpEditor.Main = main;
})(jQuery);
