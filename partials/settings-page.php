<?php
/**
 * Represents the view for the administration dashboard.
 *
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */
?>

<div class="wrap">

	<?php if ( false !== get_transient( '_wpgp_github_token_error_message' ) ) : ?>
		<div class="error">
			<p>
				<?php _e( 'Gist token failed to validate. Error message: ', $this->plugin_name ); ?>
				<?php echo esc_html( get_transient( '_wpgp_github_token_error_message' ) ); ?>
			</p>
		</div>
		<?php delete_transient( '_wpgp_github_token_error_message' ); ?>
	<?php endif; ?>

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<?php

		$prefix = '_wpgp_';

		cmb2_metabox_form( array(
			'id'         => 'option_metabox',
			'show_on'    => array( 'key' => 'options-page', 'value' => array( $this->plugin_name ) ),
			'show_names' => true,
			'fields'     => array(
				array(
					'name' => __( 'Highlighter Theme', $this->plugin_name ),
					'desc' => __( 'This is the theme PrismJS highlights your code with. See how it works below.', $this->plugin_name ),
					'id'   => $prefix . 'gistpen_highlighter_theme',
					'type' => 'select',
					'options' => array(
						'default' => __( 'Default', $this->plugin_name ),
						'dark' => __( 'Dark', $this->plugin_name ),
						'funky' => __( 'Funky', $this->plugin_name ),
						'okaidia' => __( 'Okaidia', $this->plugin_name ),
						'twilight' => __( 'Twilight', $this->plugin_name ),
						'coy' => __( 'Coy', $this->plugin_name ),
					),
					'default' => cmb2_get_option( $this->plugin_name, $prefix . 'gistpen_highlighter_theme' )
				),
				array(
					'name' => __( 'Enable line numbers', $this->plugin_name ),
					'id'   => $prefix . 'gistpen_line_numbers',
					'type' => 'checkbox',
				),
				array(
					'name' => __( 'Add your Gist token', $this->plugin_name ),
					'id'   => $prefix . 'gist_token',
					'type' => 'text',
				),
			)
		), $this->plugin_name );
	?>

	<pre class="gistpen line-numbers" data-src="<?php echo WP_GISTPEN_URL; ?>assets/js/prism.js"></pre>

</div>
