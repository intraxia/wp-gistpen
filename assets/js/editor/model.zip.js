(function($){
	var editor = window.wpgpEditor;

	var modelzip = Backbone.Model.extend({
		defaults: {
			description: "",
			ID: null,
			status: "",
			password: "",
			sync: "off",
		},

		initialize: function() {
			if('Auto Draft' === this.get('description')) {
				this.set('description', '');
			}
			if('auto-draft' === this.get('status')) {
				this.set('status', 'draft');
			}
			if('on' !== this.get('sync')) {
				this.set('sync', 'off');
			}

			this.view = new editor.Views.Zip({model: this});
		},
	});

	window.wpgpEditor.Models.Zip = modelzip;
})(jQuery);
