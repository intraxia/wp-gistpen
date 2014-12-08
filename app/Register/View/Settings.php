<?php
namespace WP_Gistpen\Register\View;

use WP_Gistpen\Account\Gist;

/**
 * This class registers all of the settings page views
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Settings {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 * @var      string    $plugin_name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    0.1.0
	 */
	public function add_plugin_admin_menu() {

		add_options_page(
			__( 'WP-Gistpen Settings', $this->plugin_name ),
			__( 'Gistpens', $this->plugin_name ),
			'edit_posts',
			$this->plugin_name,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    0.1.0
	 */
	public function display_plugin_admin_page() {

		include_once( WP_GISTPEN_DIR . 'partials/settings-page.php' );

	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    0.1.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __( 'Settings', $this->plugin_name ) . '</a>'
			),
			$links
		);

	}

	/**
	 * Validates the OAuth token and  before save.
	 *
	 * OAuth token
	 *
	 * @param    null            $override_value null if validation fails
	 * @param    string          $value          value to validate
	 * @param    int             $object_id      CMB2_Options object id
	 * @param    array           $args           CMB2 args
	 * @param    CMB2_Sanitize   $validation_obj validation object
	 * @return   string                          empty string if token doesn't validate
	 * @since    0.5.0
	 */
	public function validate_gist_token( $override_value, $value, $object_id, $args, $validation_obj ) {
		if ( '_wpgp_gist_token' !== $args['id'] || empty( $value ) || $validation_obj->value === $validation_obj->field->value ) {
			return $value;
		}

		$client = new Gist( $this->plugin_name, $this->version );
		$client->authenticate( $value );

		if ( is_wp_error( $error = $client->check_token() ) ) {
			set_transient( '_wpgp_github_token_error_message', $error->get_error_message(), 15 );
			return '';
		}

		return $value;
	}

	/**
	 * Register the settings page (obviously)
	 *
	 * @since 0.3.0
	 */
	public function register_setting() {
		register_setting( $this->plugin_name, $this->plugin_name );
	}

}
