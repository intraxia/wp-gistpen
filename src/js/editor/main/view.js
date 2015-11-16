var $ = window.jQuery;
var Backbone = window.Backbone;

module.exports = Backbone.View.extend({
	id: 'wpgp-editor',
	template: _.template($("script#wpgpMain").html()),
	events: {
		'change select': 'updateTheme',
		'click input#wpgp-addfile': 'addFile',
		'click input#wpgp-update': 'updateGistpen',
	},

	render: function () {
		this.$el.append(this.template());

		this.$select = this.$('select');
		this.$updateGistpen = this.$('input#wpgp-update');
		this.$spinner = this.$('span.spinner');

		return this;
	},

	updateTheme: function () {
		this.model.set('acetheme', this.$select.val());
	},

	addFile: function (e) {
		e.preventDefault();

		this.model.addFile();
	},

	updateGistpen: function (e) {
		var that = this;

		e.preventDefault();

		this.$spinner.addClass('is-active');
		this.$updateGistpen.prop('disabled', true);
		this.model.updateGistpen().done(function (response) {
			that.displayMessage(response.data.code, response.data.message);

			that.$updateGistpen.prop('disabled', false);
			that.$spinner.removeClass('is-active');
		});
	},

	displayMessage: function (code, message) {
		var $message = $('<div class="' + code + '"><p>' + message + '</p></div>');
		$message.hide()
			.prependTo(this.$el)
			.slideDown('slow')
			.delay('2000')
			.slideUp('slow');
	}
});
