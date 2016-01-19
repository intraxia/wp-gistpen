<?php
namespace Intraxia\Gistpen\View;

use Intraxia\Gistpen\Client\Gist;
use Intraxia\Jaxion\Contract\Core\HasActions;
use Intraxia\Jaxion\Contract\Core\HasFilters;

/**
 * This class registers all of the settings page views
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Settings implements HasActions, HasFilters {
	/**
	 * Gist account object
	 *
	 * @var Gist
	 * @since 0.5.0
	 */
	protected $client;

	/**
	 * Plugin basename
	 *
	 * @var string
	 */
	protected $basename;

	/**
	 * Plugin path
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param Gist   $client
	 * @param string $basename
	 * @param string $path
	 *
	 * @since    0.5.0
	 */
	public function __construct( Gist $client, $basename, $path ) {
		$this->client = $client;
		$this->basename = $basename;
		$this->path = $path;
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    0.1.0
	 */
	public function add_plugin_admin_menu() {
		add_options_page(
			__( 'WP-Gistpen Settings', 'wp-gistpen' ),
			__( 'Gistpens', 'wp-gistpen' ),
			'edit_posts',
			'wp-gistpen',
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    0.1.0
	 */
	public function display_plugin_admin_page() {
		include_once( $this->path . 'partials/settings/page.php' );
	}

	/**
	 * Display GitHub user info on settings page
	 *
	 * @since 0.5.0
	 */
	public function github_user_layout() {
		$token = cmb2_get_option( 'wp-gistpen', '_wpgp_gist_token' );

		if ( false === $token ) {
			return;
		}

		$user = get_transient( '_wpgp_github_token_user_info' );

		if ( false === $user ) {
			$this->client->set_token( $token );

			if ( ! $this->client->is_token_valid() ) {
				// If this token doesn't validate, clear it and bail.
				cmb2_update_option( 'wp-gistpen', '_wpgp_gist_token', '' );
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

		<strong><?php _e( 'Username: ', 'wp-gistpen' ); ?></strong><?php echo esc_html( $login ); ?><br>
		<strong><?php _e( 'Email: ', 'wp-gistpen' ); ?></strong><?php echo esc_html( $email ); ?><br>
		<strong><?php _e( 'Public Gists: ', 'wp-gistpen' ); ?></strong><?php echo esc_html( $public_gists ); ?><br>
		<strong><?php _e( 'Private Gists: ', 'wp-gistpen' ); ?></strong><?php echo esc_html( $private_gists ); ?><br><br>

		<p class="cmb2-metabox-description">
			<?php submit_button( 'Export Gistpens', 'secondary', 'export-gistpens', false ); ?>
			<?php _e( "When you export  Gistpens, all Gistpens are exported, even if sync is unchecked. Sync will be enabled for those Gistpens; you can disable them individually.", 'wp-gistpen' ); ?>
		</p>

		<p class="cmb2-metabox-description">
			<?php submit_button( 'Import Gists', 'secondary', 'import-gists', false ); ?>
			<?php _e( "When you import Gists, only Gists not previously imported will be added.", 'wp-gistpen' ); ?>
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
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . 'wp-gistpen' ) . '">' . __( 'Settings', 'wp-gistpen' ) . '</a>'
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
						_e( 'Gist token failed to validate. Error message: ', 'wp-gistpen' );
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
		register_setting( 'wp-gistpen', 'wp-gistpen' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array[]
	 */
	public function action_hooks() {
		return array(
			array(
				'hook' => 'admin_menu',
				'method' => 'add_plugin_admin_menu',
			),
			array(
				'hook' => 'admin_init',
				'method' => 'register_setting',
			),
			array(
				'hook' => 'cmb2_before_options-page_form_wpgp_option_metabox',
				'method' => 'github_user_layout',
			),
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array[]
	 */
	public function filter_hooks() {
		return array(
			array(
				'hook' => 'cmb2_validate_text',
				'method' => 'validate_gist_token',
				'args' => 5,
			),
			array(
				'hook' => 'cmb2_get_metabox_form_format',
				'method' => 'modify_form_output',
				'args' => 3,
			),
			array(
				'hook' => 'plugin_action_links_' . $this->basename,
				'method' => 'add_action_links',
			)
		);
	}
}
