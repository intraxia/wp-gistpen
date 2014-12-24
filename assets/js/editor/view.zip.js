(function($){
	var viewzip = Backbone.View.extend({
		id: 'wpgp-zip',
		template: _.template($("script#wpgpZip").html()),
		defaults: {
			description: "",
			ID: null,
			status: "",
			password: ""
		},

		events: {
			'keyup input#title': 'updateDescription'
		},

		render: function() {
			this.$el.html( this.template( this.model.toJSON() ) );

			this.$inputDescription = this.$el.find('#title');
			this.$labelDescription = this.$el.find('#title-prompt-text');

			return this;
		},

		updateDescription: function() {
			that = this;
			this.model.set('description', this.$inputDescription.val());

			if ( '' === this.$inputDescription.val() ) {
				this.$labelDescription.removeClass('screen-reader-text');
			}

			this.$labelDescription.click(function(){
				that.$labelDescription.addClass('screen-reader-text');
				that.$inputDescription.focus();
			});

			this.$inputDescription.blur(function(){
				if ( '' === this.value ) {
					that.$labelDescription.removeClass('screen-reader-text');
				}
			}).focus(function(){
				that.$labelDescription.addClass('screen-reader-text');
			}).keydown(function(e){
				that.$labelDescription.addClass('screen-reader-text');
			});
		}
	});

	window.wpgpEditor.Views.Zip = viewzip;
})(jQuery);
