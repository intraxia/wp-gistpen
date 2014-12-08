<?php
namespace WP_Gistpen\Account;

use WP_Gistpen\Facade\Database;
use WP_Gistpen\Facade\Adapter;
use Github\Client;

/**
 * This is the class description.
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Gist {

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
	 * Database Facade object
	 *
	 * @var Database
	 * @since 0.5.0
	 */
	private $database;

	/**
	 * Adapter Facade object
	 *
	 * @var Adapter
	 * @since  0.5.0
	 */
	private $adapter;

	/**
	 * Github\Client object
	 *
	 * @var \Github\Client
	 * @since 0.5.0
	 */
	private $client;

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

		$this->database = new Database( $this->plugin_name, $this->version );
		$this->adapter = new Adapter( $this->plugin_name, $this->version );

		$this->client = new Client();

	}

	/**
	 * Add OAuth token to Github\Client
	 *
	 * Adds the supplied token to the attached Github\Client
	 * to prepare it for API communication.
	 *
	 * @param  string          $token     Authentication token
	 * @since  0.5.0
	 */
	public function authenticate( $token ) {
		$this->client->authenticate( $token, null, Client::AUTH_HTTP_TOKEN );
	}

	/**
	 * Checks if token is valid.
	 *
	 * Connects to the Github api to check if the
	 * authenticated token is valid. Returns a WP_Error
	 * object if it fails, caches the user data if it succeeds.
	 *
	 * @param  string          $token     Authentication token
	 * @return bool|WP_Error              true of success, WP_Error on failure
	 * @since  0.5.0
	 */
	public function check_token() {
		$success = true;

		try {
			$user = $this->client->api( 'me' )->show();
		} catch ( \Github\Exception\TwoFactorAuthenticationRequiredException $e ) {
			$success = new \WP_Error( $e->getCode(), $e->getMessage() );
		} catch ( \Github\Exception\RuntimeException $e ) {
			$success = new \WP_Error( $e->getCode(), $e->getMessage() );
		}

		delete_transient( '_wpgp_github_token_user_info' );

		if ( ! is_wp_error( $success ) ) {
			set_transient( '_wpgp_github_token_user_info', $user, 7 * DAY_IN_SECONDS );
		}

		return $success;
	}
}
