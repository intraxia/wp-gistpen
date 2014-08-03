<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 James DiGioia
 */
?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<?php

		$prefix = '_wpgp_';
		$instance = WP_Gistpen::get_instance();

		cmb_metabox_form( array(
			'id'         => 'option_metabox',
			'show_on'    => array( 'key' => 'options-page', 'value' => array( $instance->get_plugin_slug() ) ),
			'show_names' => true,
			'fields'     => array(
				array(
					'name' => __( 'Highlighter Theme', $instance->get_plugin_slug() ),
					'desc' => __( 'This is the theme PrismJS highlights your code with. See how it works below.', $instance->get_plugin_slug() ),
					'id'   => $prefix . 'gistpen_highlighter_theme',
					'type' => 'select',
					'options' => array(
						'default' => __( 'Default', $instance->get_plugin_slug() ),
						'dark' => __( 'Dark', $instance->get_plugin_slug() ),
						'funky' => __( 'Funky', $instance->get_plugin_slug() ),
						'okaidia' => __( 'Okaidia', $instance->get_plugin_slug() ),
						'twilight' => __( 'Twilight', $instance->get_plugin_slug() ),
						'coy' => __( 'Coy', $instance->get_plugin_slug() ),
					),
					'default' => cmb_get_option( $instance->get_plugin_slug(), $prefix . 'gistpen_highlighter_theme' )
				),
				array(
					'name' => __( 'Enable line numbers', $instance->get_plugin_slug() ),
					'id'   => $prefix . 'gistpen_line_numbers',
					'type' => 'checkbox',
				),
			)
		), $instance->get_plugin_slug() );
	?>

	<pre class="gistpen line-numbers" data-src="<?php echo WP_GISTPEN_URL; ?>public/assets/vendor/prism/components/prism-core.js"></pre>

</div>
