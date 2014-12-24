(function($){
	var editor = window.wpgpEditor;

	var modelzip = Backbone.Model.extend({
		initialize: function() {
			if('Auto Draft' === this.get('description')) {
				this.set('description', '');
			}

			this.view = new editor.Views.Zip({model: this});
		},
	});

	window.wpgpEditor.Models.Zip = modelzip;
})(jQuery);
