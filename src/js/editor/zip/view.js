var $ = window.jQuery;
var Backbone = window.Backbone;

module.exports = Backbone.View.extend({
	id: 'wpgp-zip',
	template: _.template($("script#wpgpZip").html()),

	events: {
		'keyup input#title': 'updateDescription',
		'change select#wpgp-zip-status': 'updateLanguage',
		'click .wpgp-sync': 'updateSync',
	},

	render: function() {
		var checked;

		this.$el.html( this.template( this.model.toJSON() ) );

		this.$inputDescription = this.$el.find('#title');
		this.$labelDescription = this.$el.find('#title-prompt-text');
		this.$selectStatus = this.$('#wpgp-zip-status');
		this.$inputSync = this.$('#wpgp-zip-sync');

		if ( '' !== this.model.get('description') ) {
			this.$labelDescription.addClass('screen-reader-text');
		}

		this.$selectStatus.val(this.model.get('status'));

		if ( 'on' === this.model.get('sync')) {
			checked = true;
		} else {
			checked = false;
		}

		this.$inputSync.prop('checked', checked);

		this.addListeners();

		return this;
	},

	addListeners: function() {
		that = this;

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
	},

	updateDescription: function() {
		this.model.set('description', this.$inputDescription.val());
	},

	updateLanguage: function() {
		this.model.set('status', this.$selectStatus.val());
	},

	updateSync: function() {
		var sync;

		if (true === this.$inputSync.prop('checked')) {
			sync = 'on';
		} else {
			sync = 'off';
		}

		this.model.set('sync', sync);
	}
});
