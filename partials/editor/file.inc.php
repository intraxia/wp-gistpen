<script type="text/template" id="wpgpFile">

<div class="wp-editor-tools hide-if-no-js">

	<div class="wp-media-buttons">
		<input type="text" size="20" class="wpgp-file-slug" placeholder="<?php _e( 'Filename', $this->plugin_name ); ?>" autocomplete="off" value="<%- slug %>" />
		<select class="wpgp-file-lang">
			<?php foreach ( WP_Gistpen\Model\Language::$supported as $lang => $slug ) : ?>
				<option value="<?php echo $slug; ?>">
					<?php echo $lang; ?>
				</option>
			<?php endforeach; ?>
		</select>
		<button type="submit" class="button delete" value="Delete This Gistfile">Delete This Gistfile</button>
	</div>

	<div class="wp-editor-tabs wp-gistpenfile-editor-tabs">
		<a class="hide-if-no-js wp-switch-editor switch-text">Text</a>
		<a class="hide-if-no-js wp-switch-editor switch-ace">Ace</a>
	</div>

</div>

<div class="wp-editor-container wp-gistpenfile-editor-container">
	<textarea class="wp-editor-area wpgp-code" cols="40" rows="20"><%- code %></textarea>
	<div class="ace-editor"></div>
</div>

</script>
