(function($){
	var viewfiles = Backbone.View.extend({
		id: 'wpgp-files',

		render: function() {
			this.collection.each( this.addAce, this );

			return this;
		},

		addAce : function(model, index) {
			this.$el.append( model.view.render().el );
		},

		updateThemes: function(theme) {
			this.theme = theme;
			this.collection.each( this.updateTheme, this );
		},

		updateTheme: function(model, index) {
			model.view.updateTheme(this.theme);
		},
	});

	window.wpgpEditor.Views.Files = viewfiles;
})(jQuery);
