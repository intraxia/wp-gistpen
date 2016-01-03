var $ = window.jQuery;
var Backbone = window.Backbone;

module.exports = Backbone.View.extend({
	id: 'wpgp-files',

	render: function () {
		this.collection.each(this.addAce, this);

		return this;
	},

	addAce: function (model) {
		this.$el.append(model.view.render().el);
	},

	updateThemes: function (theme) {
		this.collection.each(function (model) {
			model.view.updateTheme(theme);
		});
	}
});
