var $ = window.jQuery;
var Backbone = window.Backbone;
var View = require('./view');
var ZipModel = require('../zip/model');
var FileCollection = require('../file/collection');

module.exports = Backbone.Model.extend({
	$nonce: $('#_ajax_wp_gistpen'),
	defaults: {
		acetheme: 'ambiance'
	},

	initialize: function() {
		this.getData();

		this.view = new View({model: this});
	},

	attachListeners: function() {
		this.on({
			'change:acetheme': this.updateThemes
		});
	},

	updateThemes: function() {
		var that = this;

		this.files.view.updateThemes(this.get('acetheme'));

		$.post(ajaxurl, {
			nonce: that.getNonce(),
			action: 'save_ace_theme',
			theme: that.get('acetheme')
		}, function(response, textStatus, xhr) {
			if(response.success === false) {
				that.view.displayMessage(response.data.code, response.data.message);
			}
		});
	},

	getData: function() {
		$.ajaxq('getData', {
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'get_gistpen',
				nonce: this.getNonce(),
				post_id: this.get('post_id')
			},
			context: this
		})
		.done(function(response) {
			if(response.success === false) {
				this.view.displayMessage(response.data.code, response.data.message);
			} else {
				this.set('files', response.data.files);
				delete response.data.files;

				this.set('zip', response.data);

				this.zip = new ZipModel(this.get('zip'));
				this.files = new FileCollection(this.get('files'));

				this.get('form').prepend(this.render());
			}
		});

		$.ajaxq('getData', {
			url: ajaxurl,
			type: 'POST',
			data: {
				nonce: this.getNonce(),
				action: 'get_ace_theme'
			},
			context: this
		})
		.done(function(response) {
			if(response.success === true && "" !== response.data.theme) {
				this.set('acetheme', response.data.theme);
				this.view.$select.val(this.get('acetheme'));
			}

			this.files.view.updateThemes(this.get('acetheme'));

			this.attachListeners();
		});

	},

	addFile: function() {
		var file = new editor.Models.File();

		this.files.add(file);
		this.files.view.updateThemes(this.get('acetheme'));
	},

	updateGistpen: function() {
		var that = this;

		return $.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'save_gistpen',
				nonce: that.getNonce(),

				zip: that.toJSON()
			},
		});
	},

	toJSON: function() {
		var atts = _.clone( this.zip.attributes );
		atts.files = _.clone( this.files.toJSON() );

		return atts;
	},

	getNonce: function() {
		return $.trim(this.$nonce.val());
	},

	render: function() {
		this.view.render();
		this.view.$el.prepend( this.zip.view.render().el );
		this.view.$el.find('.wpgp-main-settings').after( this.files.view.render().el );

		return this.view.$el;
	},

});
