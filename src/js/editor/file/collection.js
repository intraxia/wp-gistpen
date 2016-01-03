var $ = window.jQuery;
var Backbone = window.Backbone;
var CollectionView = require('./view/collection');
var FileModel = require('./model');

module.exports = Backbone.Collection.extend({
	model: function (attrs, options) {
		return new FileModel(attrs, options);
	},

	initialize: function () {
		this.view = new CollectionView({collection: this});
		this.on({
			'add': this.addFile
		});
	},

	addFile: function () {
		this.view.render();
	}
});
