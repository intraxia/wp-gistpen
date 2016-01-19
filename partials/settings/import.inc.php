<!-- Templates -->
<script type="text/template" id="importHeaderTemplate">
<h2><?php _e( 'Importing Gists...', 'wp-gistpen' ); ?></h2>

<p><a href="<?php echo esc_html( admin_url( 'options-general.php?page=' . 'wp-gistpen' ) ); ?>"><?php _e( 'Return to WP-Gistpen Settings', 'wp-gistpen' ); ?></a></p>

<div id="import-status">
	<div id="progressbar"></div>
</div>
</script>

