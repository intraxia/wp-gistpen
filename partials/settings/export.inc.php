<!-- Templates -->
<script type="text/template" id="exportHeaderTemplate">
<h2><?php _e( 'Exporting Gistpens...', 'wp-gistpen' ); ?></h2>

<p><a href="<?php echo esc_html( admin_url( 'options-general.php?page=' . 'wp-gistpen' ) ); ?>"><?php _e( 'Return to WP-Gistpen Settings', 'wp-gistpen' ); ?></a></p>

<div id="export-status">
	<div id="progressbar"></div>
</div>
</script>

