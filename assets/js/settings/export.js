(function($){
	var exp = Backbone.Model.extend({
		initialize: function(opts) {
			this.startBtn = jQuery('#export-gistpens');
			this.wrap = jQuery('.wpgp-wrap');
			this.templates = {};
			this.templates.header = jQuery("script#exportHeaderTemplate");
			this.templates.progress = jQuery("script#progressBarTemplate");
			this.templates.status = jQuery("script#statusTemplate");
		},

		setClickHandlers: function () {
			var that = this;

			if(!this.startBtn.length) {
				return;
			}

			this.startBtn.prop("disabled", true);

			this.getGistpenIDs();

			this.startBtn.click(function(e) {
					e.preventDefault();

					that.wrap.html('');

					that.appendHeader();

					that.gistpenIDs.forEach(function(id) {
						that.exportID(id);
					});
			});
		},

		getGistpenIDs: function() {
			var that = this;
			jQuery.post(ajaxurl, {
				action: 'get_gistpens_missing_gist_id',
				nonce: jQuery('#_ajax_wp_gistpen').val()
			}, function(response) {
				if(false === response.success) {
					that.startBtn.val(response.data.message);
				} else {
					that.gistpenIDs = response.data.ids;
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
			this.$status = jQuery('#export-status');

			var result = this.$progress.progressbar({
				max: that.gistpenIDs.length,
				value: 0,
				enable: true
			});
		},

		exportID: function(id) {
			var that = this;

			jQuery.ajaxq('export', {
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'create_gist_from_gistpen_id',
					nonce: jQuery('#_ajax_wp_gistpen').val(),

					gistpen_id: id
				},
				success: function(response) {

				},
				error: function(response) {
					jQuery.ajaxq.abort('export');
				},
				complete: function(jqxhr) {
					that.updateProgress(jqxhr);
				}
			});
		},

		updateProgress: function(jqxhr) {
			var response = jqxhr.responseJSON;
			var template = _.template(this.templates.status.html());

			this.$progress.progressbar( 'value', this.$progress.progressbar("value") + 1);

			this.$status.append(template({
				status_code: response.data.code,
				status_message: response.data.message
			}).trim());
		}
	});

	window.wpgpSettings.Export = exp;
})(jQuery);
