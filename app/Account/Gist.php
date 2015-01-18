<?php
namespace WP_Gistpen\Account;

use WP_Gistpen\Facade\Database;
use WP_Gistpen\Facade\Adapter;
use Github\Client;
use Github\ResultPager;

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
	 * Adapter Facade object
	 *
	 * @var Adapter
	 * @since  0.5.0
	 */
	protected $adapter;

	/**
	 * Github\Client object
	 *
	 * @var \Github\Client
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

		$this->adapter = new Adapter( $this->plugin_name, $this->version );
		$this->client = new Client();

	}

	/**
	 * Sets up the client object with
	 * the authentication token and
	 * checks if the token is still valid
	 *
	 * @since 0.5.0
	 */
	private function set_up_client() {
		$token = (string) cmb2_get_option( $this->plugin_name, '_wpgp_gist_token' );

		if ( empty( $token ) ) {
			return new \WP_Error( 'no_github_token', 'No GitHub OAuth token available.' );
		}

		$this->authenticate( $token );

		return $token;
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
	 * @return bool|\WP_Error              true of success, WP_Error on failure
	 * @since  0.5.0
	 */
	public function check_token() {
		$success = true;

		try {
			$user = $this->show_me();
		} catch ( \Exception $e ) {
			$success = new \WP_Error( $e->getCode(), $e->getMessage() );
		}

		delete_transient( '_wpgp_github_token_user_info' );

		if ( ! is_wp_error( $success ) ) {
			set_transient( '_wpgp_github_token_user_info', $user );
		}

		return $success;
	}

	/**
	 * Creates a new Gist based on History
	 *
	 * @param  Commit           $comit    Gist data
	 * @return string|\WP_Error           Gist id on success, WP_Error on failure
	 * @since  0.5.0
	 */
	public function create_gist( $commit ) {
		$result = $this->set_up_client();

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$gist = $this->adapter->build( 'gist' )->create_by_commit( $commit );

		try {
			$response = $this->call()->create( $gist );
		} catch ( \Exception $e ) {
			$response = new \WP_Error( $e->getCode(), $e->getMessage() );
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return $response;
	}

	/**
	 * Update an existing Gist based on Zip
	 *
	 * @param  Zip             $history    Gist data
	 * @return string|\WP_Error         Gist id on success, WP_Error on failure
	 * @since  0.5.0
	 */
	public function update_gist( $commit ) {
		$result = $this->set_up_client();

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$gist = $this->adapter->build( 'gist' )->update_by_commit( $commit );

		try {
			$response = $this->call()->update( $commit->get_head_gist_id(), $gist );
		} catch ( \Exception $e ) {
			$response = new \WP_Error( $e->getCode(), $e->getMessage() );
		}

		return $response;
	}

	/**
	 * Retrieves all the Gist IDs for the current user
	 *
	 * @return array      array of gist IDs
	 * @since  0.5.0
	 */
	public function get_gists() {
		$result = $this->set_up_client();

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$gists = array();

		try {
			$response = $this->all();
		} catch ( \Exception $e ) {
			$response = new \WP_Error( $e->getCode(), $e->getMessage() );
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		foreach ( $response as $gist ) {
			$gists[] = $gist['id'];
		}

		return $gists;
	}

	/**
	 * Retrieves an individual Gist
	 *
	 * @param  string $id Gist id
	 * @return array      array response with Zip and commit version sha
	 * @since  0.5.0
	 */
	public function get_gist( $id ) {
		$result = $this->set_up_client();

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		try {
			$response = $this->call()->show( $id );
		} catch ( \Exception $e ) {
			$response = new \WP_Error( $e->getCode(), $e->getMessage() );
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$zip = $this->adapter->build( 'zip' )->by_gist( $response );

		foreach ( $response['files'] as $filename => $file_data ) {
			$file = $this->adapter->build( 'file' )->by_gist( $file_data );
			$file->set_language( $this->adapter->build( 'language' )->by_gist( $file_data['language'] ) );

			$zip->add_file( $file );
		}

		$result = array(
			'zip' => $zip,
			'version' => $response['history'][0]['version']
		);

		return $result;
	}

	/**
	 * Shortcut to call the Gist API
	 * Makes the class easier to test
	 *
	 * @return \Github\Client\Api\Gists
	 * @since 0.5.0
	 */
	protected function call() {
		return $this->client->api( 'gists' );
	}

	/**
	 * Retrieves all the Gists using the ResultPager api
	 *
	 * @return array All the Gists for the logged in use
	 * @since  0.5.0
	 */
	protected function all() {
		$pager = new ResultPager( $this->client );
		return $pager->fetchAll( $this->call(), 'all' );
	}

	/**
	 * Shortcut to get all information
	 * for the current user from the
	 * Github API
	 *
	 * @return \Github\Api\CurrentUser
	 * @since 0.5.0
	 */
	protected function show_me() {
		return $this->client->api( 'me' )->show();
	}
}
