<script type="text/template" id="wpgpZip">

<div id="titlediv">
	<div id="titlewrap">
		<label id="title-prompt-text" for="title"><?php _e( 'Gistpen description...', $this->plugin_name ); ?></label>
		<input type="text" name="post_title" size="30" value="<%- description %>" id="title" spellcheck="true" autocomplete="off">
	</div>
</div>

<div class="wpgp-zip-settings">
	<label for="wpgp-zip-status" id="zip-status-text"><?php _e( 'Post Status:', $this->plugin_name ); ?></label>
	<select class="wpgp-zip-status">
		<?php foreach ( get_post_statuses() as $slug => $status ) : ?>
			<option value="<?php echo $slug; ?>">
				<?php echo $status; ?>
			</option>
		<?php endforeach; ?>
	</select>
</div>

</script>
