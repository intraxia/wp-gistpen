<script type="text/template" id="wpgpMain">

<div class="wpgp-ace-settings">
	<label id="ace-theme-select" for="_wpgp_ace_theme"><?php _e( 'Ace Editor Theme: ', $this->plugin_name ); ?></label>
	<select name="_wpgp_ace_theme" id="_wpgp_ace_theme">
	<?php foreach ( $this->ace_themes as $slug => $name ) : ?>
		<option value="<?php echo $slug; ?>"><?php echo $name; ?></option>
	<?php endforeach; ?>
	</select>
</div>

<input type="submit" name="wpgp-addfile" id="wpgp-addfile" class="button button-primary" value="Add Gistfile">

</script>
