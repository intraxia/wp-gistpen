(function($){
	var viewfiles = Backbone.View.extend({
		id: 'wpgp-files',

		render: function() {
			this.collection.each(this.addAce, this);

			return this;
		},

		addAce : function(model, index) {
			this.$el.append(model.view.render().el);
		},

		updateThemes: function(theme) {
			this.collection.each(function(model, index) {
				model.view.updateTheme(theme);
			});
		}
	});

	window.wpgpEditor.Views.Files = viewfiles;
})(jQuery);
