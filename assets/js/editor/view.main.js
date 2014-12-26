(function($){
	var viewmain = Backbone.View.extend({
		id: 'wpgp-editor',
		template: _.template($("script#wpgpMain").html()),
		events : {
			'change select': 'updateTheme',
			'click input#wpgp-addfile' : 'addFile'
		},

		render: function() {
			this.$el.append( this.template() );

			this.$select = this.$el.find('select');
			this.$addFile = this.$el.find('input#wpgp-addfile');

			return this;
		},

		updateTheme: function() {
			this.model.set('acetheme', this.$select.val());
		},

		addFile: function(e) {
			e.preventDefault();

			this.model.addFile();
		}
	});

	window.wpgpEditor.Views.Main = viewmain;
})(jQuery);
