var $ = window.jQuery;
var Backbone = window.Backbone;
var View = require('./view/model');

module.exports = Backbone.Model.extend({
	defaults: {
		slug: "",
		code: "",
		ID: null,
		language: ""
	},

	initialize: function() {
		this.view = new View({model: this});
	},

	deleteFile: function() {
		this.collection.remove(this);
	}
});
