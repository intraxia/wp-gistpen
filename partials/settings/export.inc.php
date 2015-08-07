<!-- Templates -->
<script type="text/template" id="exportHeaderTemplate">
<h2><?php _e( 'Exporting Gistpens...', \Gistpen::$plugin_name ); ?></h2>

<p><a href="<?php echo esc_html( admin_url( 'options-general.php?page=' . \Gistpen::$plugin_name ) ); ?>"><?php _e( 'Return to WP-Gistpen Settings', \Gistpen::$plugin_name ); ?></a></p>

<div id="export-status">
	<div id="progressbar"></div>
</div>
</script>

