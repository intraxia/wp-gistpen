<!-- Templates -->
<script type="text/template" id="importHeaderTemplate">
<h2><?php _e( 'Importing Gists...', \WP_Gistpen::$plugin_name ); ?></h2>

<p><a href="<?php echo esc_html( admin_url( 'options-general.php?page=' . \WP_Gistpen::$plugin_name ) ); ?>"><?php _e( 'Return to WP-Gistpen Settings', \WP_Gistpen::$plugin_name ); ?></a></p>

<div id="import-status">
	<div id="progressbar"></div>
</div>
</script>

