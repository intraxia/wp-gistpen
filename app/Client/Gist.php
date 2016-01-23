<?php
namespace Intraxia\Gistpen\Client;

use Exception;
use Intraxia\Gistpen\Facade\Adapter;
use Github\Client;
use Github\ResultPager;
use Intraxia\Gistpen\Model\Commit\Meta;
use Intraxia\Gistpen\Model\File;
use Intraxia\Gistpen\Model\Zip;
use WP_Error;

/**
 * This is the class description.
 *
 * @package    Intraxia\Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Gist {
	/**
	 * Adapter Facade object.
	 *
	 * @var Adapter
	 * @since  0.5.0
	 */
	protected $adapter;

	/**
	 * GitHub client.
	 *
	 * @var \Github\Client
	 * @since 0.5.0
	 */
	protected $client;

	/**
	 * Whether the class has a token to start making requests with.
	 *
	 * @var bool
	 */
	protected $ready = false;

	/**
	 * Error object for previous error.
	 *
	 * @var null|WP_Error
	 */
	protected $error = null;

	/**
	 * Initialize the Gist class and sets its properties.
	 *
	 * @since    0.5.0
	 *
	 * @param Adapter $adapter
	 * @param Client  $client
	 */
	public function __construct( Adapter $adapter, Client $client ) {
		$this->adapter = $adapter;
		$this->client  = $client;
	}

	/**
	 * Sets the GitHub client's authentication token
	 * and puts the class into the "ready" state.
	 *
	 * @since 0.6.0
	 *
	 * @param string $token
	 */
	public function set_token( $token ) {
		if ( $token ) {
			$this->client->authenticate( $token, null, Client::AUTH_HTTP_TOKEN );
			$this->ready = true;
		} else {
			$this->ready = false;
		}
	}

	/**
	 * Retrieves the last error.
	 *
	 * If there was no error previously, the last error will be returned as `null`.
	 *
	 * @return null|WP_Error
	 */
	public function get_error() {
		return $this->error;
	}

	/**
	 * Checks if account is ready to call Gist API.
	 *
	 * If the client is not in the "ready" state, attempts to retrieve the token
	 * from the database and set it. Returns the current ready state after attempting.
	 *
	 * @return bool
	 */
	public function is_ready() {
		if ( ! $this->ready && $token = (string) cmb2_get_option( 'wp-gistpen', '_wpgp_gist_token' ) ) {
			$this->set_token( $token );
		}

		return $this->ready;
	}

	/**
	 * Returns whether a token is valid.
	 *
	 * Connects to the GitHub api and attempts to retrieve user information with
	 * the supplied token. Returns a boolean indicating whether retrieval succeeded.
	 *
	 * @return bool
	 * @since  0.5.0
	 */
	public function is_token_valid() {
		if ( ! $this->is_ready() ) {
			$this->set_error_not_ready();

			return false;
		}

		delete_transient( '_wpgp_github_token_user_info' );

		try {
			set_transient( '_wpgp_github_token_user_info', $this->client->api( 'me' )
				->show() );

			return true;
		} catch ( Exception $e ) {
			$this->error = new WP_Error( $e->getCode(), $e->getMessage() );

			return false;
		}
	}

	/**
	 * Creates a new Gist from a Commit.
	 *
	 * Prepares the commit Meta and its set of states and creates a new Gist
	 * from that commit meta.
	 *
	 * @param Meta $commit
	 *
	 * @return false|array
	 * @since  0.5.0
	 */
	public function create( Meta $commit ) {
		if ( ! $this->is_ready() ) {
			$this->set_error_not_ready();

			return false;
		}

		$gist = $this->adapter->build( 'gist' )
			->create_by_commit( $commit );

		try {
			return $this->client->api( 'gists' )
				->create( $gist );
		} catch ( Exception $e ) {
			$this->error = new WP_Error( $e->getCode(), $e->getMessage() );

			return false;
		}
	}

	/**
	 * Update an existing Gist based on Zip
	 *
	 * @param  \Intraxia\Gistpen\Model\Commit\Meta $commit
	 *
	 * @return bool
	 * @since  0.5.0
	 */
	public function update( Meta $commit ) {
		if ( ! $this->is_ready() ) {
			$this->set_error_not_ready();

			return false;
		}

		$gist = $this->adapter->build( 'gist' )
			->update_by_commit( $commit );

		try {
			return $this->client->api( 'gists' )
				->update( $commit->get_head_gist_id(), $gist );
		} catch ( Exception $e ) {
			$this->error = new WP_Error( $e->getCode(), $e->getMessage() );

			return false;
		}
	}

	/**
	 * Retrieves all the Gist IDs for the current user
	 *
	 * @return false|string[]
	 * @since  0.5.0
	 */
	public function all() {
		if ( ! $this->is_ready() ) {
			$this->set_error_not_ready();

			return false;
		}

		try {
			$pager    = new ResultPager( $this->client );
			$response = $pager->fetchAll( $this->client->api( 'gists' ), 'all' );

			$gists = array();

			foreach ( $response as $gist ) {
				$gists[] = $gist['id'];
			}

			return $gists;
		} catch ( Exception $e ) {
			$this->error = new WP_Error( $e->getCode(), $e->getMessage() );

			return false;
		}
	}

	/**
	 * Retrieves an individual Gist
	 *
	 * @param  string $id Gist id
	 *
	 * @return false|array      array response with Zip and commit version sha
	 * @since  0.5.0
	 */
	public function get( $id ) {
		if ( ! $this->is_ready() ) {
			$this->set_error_not_ready();

			return false;
		}

		try {
			$response = $this->client->api( 'gists' )
				->show( $id );

			/** @var Zip $zip */
			$zip = $this->adapter->build( 'zip' )
				->by_gist( $response );

			foreach ( $response['files'] as $filename => $file_data ) {
				/** @var File $file */
				$file = $this->adapter->build( 'file' )
					->by_gist( $file_data );
				$file->set_language( $this->adapter->build( 'language' )
					->by_gist( $file_data['language'] ) );

				$zip->add_file( $file );
			}

			$result = array(
				'zip'     => $zip,
				'version' => $response['history'][0]['version'],
			);

			return $result;
		} catch ( Exception $e ) {
			$this->error = new WP_Error( $e->getCode(), $e->getMessage() );

			return false;
		}
	}

	/**
	 * Returns the default error when no token is available.
	 *
	 * @return WP_Error
	 */
	protected function set_error_not_ready() {
		$this->error = new WP_Error( 'noToken', __( 'No GitHub OAuth token found.', 'wp-gistpen' ) );
	}
}
