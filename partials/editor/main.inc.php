<script type="text/template" id="wpgpMain">

<div class="wpgp-main-settings">
	<label id="ace-theme-select" for="_wpgp_ace_theme"><?php _e( 'Ace Editor Theme: ', $this->plugin_name ); ?></label>
	<select name="_wpgp_ace_theme" id="_wpgp_ace_theme">
	<?php foreach ( $this->ace_themes as $slug => $name ) : ?>
		<option value="<?php echo $slug; ?>"><?php echo $name; ?></option>
	<?php endforeach; ?>
	</select>
	<input type="submit" name="wpgp-update" id="wpgp-update" class="button button-primary" value="Update Gistpen">
	<span class="spinner" style="display: none;"></span>
</div>

<input type="submit" name="wpgp-addfile" id="wpgp-addfile" class="button button-primary" value="Add Gistfile">

</script>
