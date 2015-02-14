<?php
namespace WP_Gistpen\View;

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
	 * Gist account object
	 *
	 * @var Gist
	 * @since 0.5.0
	 */
	public $client;

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

		$this->client = new Gist( $plugin_name, $version );

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

		include_once( WP_GISTPEN_DIR . 'partials/settings/page.php' );

	}

	/**
	 * Display GitHub user info on settings page
	 *
	 * @since 0.5.0
	 */
	public function github_user_layout() {
		$token = cmb2_get_option( $this->plugin_name, '_wpgp_gist_token' );

		if ( false === $token ) {
			return;
		}

		$user = get_transient( '_wpgp_github_token_user_info' );

		if ( false === $user ) {
			$this->client->authenticate( $token );

			if ( is_wp_error( $error = $this->client->check_token() ) ) {
				// If this token doesn't validate, clear it and bail.
				cmb2_update_option( $this->plugin_name, '_wpgp_gist_token', '' );
				delete_transient( '_wpgp_github_token_user_info' );
				return;
			}

			$user = get_transient( '_wpgp_github_token_user_info' );
		}

		$login = array_key_exists('login', $user) ? $user['login'] : '';
		$email = array_key_exists('email', $user) ? $user['email'] : '';
		$public_gists = array_key_exists('public_gists', $user) ? $user['public_gists'] : '0';
		$private_gists = array_key_exists('private_gists', $user) ? $user['private_gists'] : '0';

		?><h3>Authorized User</h3>

		<strong><?php _e( 'Username: ', $this->plugin_name ); ?></strong><?php echo esc_html( $login ); ?><br>
		<strong><?php _e( 'Email: ', $this->plugin_name ); ?></strong><?php echo esc_html( $email ); ?><br>
		<strong><?php _e( 'Public Gists: ', $this->plugin_name ); ?></strong><?php echo esc_html( $public_gists ); ?><br>
		<strong><?php _e( 'Private Gists: ', $this->plugin_name ); ?></strong><?php echo esc_html( $private_gists ); ?><br><br>

		<p class="cmb2-metabox-description">
			<?php submit_button( 'Export Gistpens', 'secondary', 'export-gistpens', false ); ?>
			<?php _e( "When you export  Gistpens, all Gistpens are exported, even if sync is unchecked. Sync will be enabled for those Gistpens; you can disable them individually.", $this->plugin_name ); ?>
		</p>

		<p class="cmb2-metabox-description">
			<?php submit_button( 'Import Gists', 'secondary', 'import-gists', false ); ?>
			<?php _e( "When you import Gists, only Gists not previously imported will be added.", $this->plugin_name ); ?>
		</p>
		<?php
	}

	/**
	 * Modify CMB2's form output to validate
	 * @param  string $form_format CMB2's form format
	 * @param  string $object_id   CMB2's form object ID
	 * @param  obj    $cmb         CMB2 object
	 * @return string              modified form format
	 */
	public function modify_form_output( $form_format, $object_id, $cmb ) {
		if ( 'wp-gistpen' == $object_id && 'wpgp_option_metabox' == $cmb->cmb_id ) {
			$form_format = '<form class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data"><input type="hidden" name="object_id" value="%2$s">%3$s<input type="submit" name="submit-cmb" value="%4$s" class="button-primary"></form>';
		}

		return $form_format;
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
	 * @param    null            $override_value null if validation fails
	 * @param    string          $value          value to validate
	 * @param    int             $object_id      CMB2_Options object id
	 * @param    array           $args           CMB2 args
	 * @param    \CMB2_Sanitize  $validation_obj validation object
	 * @return   string|null                     string if success, null if fail
	 * @since    0.5.0
	 */
	public function validate_gist_token( $override_value, $value, $object_id, $args, $validation_obj ) {
		if ( 'wp-gistpen' !== $object_id || empty( $value ) || $validation_obj->value === $validation_obj->field->value ) {
			return $value;
		}

		$this->client->authenticate( $value );

		if ( is_wp_error( $error = $this->client->check_token() ) ) {
			delete_transient( '_wpgp_github_token_user_info' ); ?>

			<div class="error">
				<p>
					<?php
						_e( 'Gist token failed to validate. Error message: ', $this->plugin_name );
						echo esc_html( $error->get_error_message() );
					?>
				</p>
			</div><?php

			$value = $override_value;
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
