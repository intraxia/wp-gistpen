(function($){
	var viewmain = Backbone.View.extend({
		id: 'wpgp-editor',
		template: _.template($("script#wpgpMain").html()),
		events : {
			'change select': 'updateTheme',
			'click input#wpgp-addfile' : 'addFile',
			'click input#wpgp-update' : 'updateGistpen',
		},

		render: function() {
			this.$el.append( this.template() );

			this.$select = this.$el.find('select');
			this.$addFile = this.$el.find('input#wpgp-addfile');
			this.$updateGistpen = this.$el.find('input#wpgp-update');
			this.$spinner = this.$el.find('span.spinner');

			return this;
		},

		updateTheme: function() {
			this.model.set('acetheme', this.$select.val());
		},

		addFile: function(e) {
			e.preventDefault();

			this.model.addFile();
		},

		updateGistpen: function(e) {
			var that = this;

			e.preventDefault();

			this.$spinner.toggle();
			this.$updateGistpen.prop('disabled', true);
			this.model.updateGistpen().done(function(response) {
				that.displayMessage(response.data.code, response.data.message);

				that.$updateGistpen.prop('disabled', false);
				that.$spinner.toggle();
			});
		},

		displayMessage: function(code, message) {
			var $message = $('<div class="'+code+'"><p>'+message+'</p></div>');
			$message.hide().prependTo(this.$el).slideDown('slow').delay('2000').slideUp('slow');
		}
	});

	window.wpgpEditor.Views.Main = viewmain;
})(jQuery);
