module.exports = Backbone.Model.extend({
	initialize: function(opts) {
		this.startBtn = jQuery('#import-gists');
		this.wrap = jQuery('.wpgp-wrap');
		this.templates = {};
		this.templates.header = jQuery("script#importHeaderTemplate");
		this.templates.status = jQuery("script#statusTemplate");
	},

	setClickHandlers: function () {
		var that = this;

		if(!this.startBtn.length) {
			return;
		}

		this.startBtn.prop("disabled", true);

		this.getGistIDs();

		this.startBtn.click(function(e) {
				e.preventDefault();

				that.wrap.html('');

				that.appendHeader();

				that.gistIDs.forEach(function(id) {
					that.importID(id);
				});
		});
	},

	getGistIDs: function() {
		var that = this;
		jQuery.post(ajaxurl, {
			action: 'get_new_user_gists',
			nonce: jQuery('#_ajax_wp_gistpen').val()
		}, function(response) {
			if(false === response.success) {
				that.startBtn.val(response.data.message);
			} else {
				that.gistIDs = response.data.gist_ids;
				that.startBtn.prop("disabled", false);
			}
		});
	},

	appendHeader: function() {
		var that = this;
		var template = _.template(this.templates.header.html());

		this.header = jQuery(template({}).trim()).appendTo(this.wrap);
		this.backLink = this.header.find("a");
		this.$progress = this.header.find("#progressbar");
		this.$progressLabel = this.header.find(".progress-label");
		this.$status = jQuery('#import-status');

		var result = this.$progress.progressbar({
			max: that.gistIDs.length,
			value: 0,
			enable: true
		});
	},

	importID: function(id) {
		var that = this;

		jQuery.ajaxq('import', {
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'import_gist',
				nonce: jQuery('#_ajax_wp_gistpen').val(),

				gist_id: id
			},
			success: function(response) {
				that.updateProgress(response);
			},
			error: function(response) {
				jQuery.ajaxq.abort('import');
				that.updateProgress(response);
			},
		});
	},

	updateProgress: function(response) {
		var template = _.template(this.templates.status.html());

		this.$progress.progressbar( 'value', this.$progress.progressbar("value") + 1);

		this.$status.append(template({
			status_code: response.data.code,
			status_message: response.data.message
		}).trim());
	}
});
