<?php
namespace Intraxia\Gistpen\Jobs;

use Intraxia\Gistpen\Client\Gist;
use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Model\Klass;
use Intraxia\Gistpen\Model\Language;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Jaxion\Axolotl\Collection;
use Intraxia\Jaxion\Contract\Axolotl\EntityManager;
use stdClass;
use WP_Error;

/**
 * Class ImportJob
 *
 * @package    Intraxia\Gistpen
 * @subpackage Jobs
 */
class ImportJob extends AbstractJob {
	/**
	 * Import target client.
	 *
	 * @var Gist
	 */
	private $client;

	/**
	 * ImportJob constructor.
	 *
	 * @param EntityManager $em
	 * @param Gist          $client
	 */
	public function __construct( EntityManager $em, Gist $client ) {
		parent::__construct( $em );
		$this->client = $client;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string
	 */
	protected function name() {
		return 'Import';
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string
	 */
	protected function slug() {
		return 'import';
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string
	 */
	protected function description() {
		return __( 'Import all imported GitHub gists.', 'wp-gistpen' );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return Collection|WP_Error
	 */
	protected function fetch_items() {
		$response = $this->client->all();

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return new Collection( 'stdClass', $response->json );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param stdClass $gist
	 *
	 * @return null|Repo
	 */
	protected function process_item( $gist ) {
		if ( ! ( $gist instanceof stdClass ) ) {
			$this->log(
				sprintf(
					__( 'Expected to see Gist data, got %s instead.', 'wp-gistpen' ),
					gettype( $gist )
				),
				Level::ERROR
			);

			return null;
		}

		$response = $this->client->one( $gist->id );

		if ( is_wp_error( $response ) ) {
			$this->log_reponse_error( $gist, $response );

			return null;
		} else {
			$gist = $response->json;
		}

		$repos = $this->em->find_by( Klass::REPO, array(
			'gist_id' => $gist->id,
			'with' => array(
				'blobs' => array(
					'with' => 'language',
				),
			),
		) );

		if ( $repos->count() === 0 ) {
			return $this->create_repo_for_gist( $gist );
		}

		foreach ( $repos as $repo ) {
			$this->update_repo_for_gist( $repo, $gist );
		}

		return null;
	}

	/**
	 * Creates a new Gist for the provided Repo.
	 *
	 * @param stdClass $gist Response from Gist.
	 *
	 * @return null
	 */
	private function create_repo_for_gist( stdClass $gist ) {
		$response = $this->em->persist( $this->map_gist_to_new_entity( $gist ) );

		if ( is_wp_error( $response ) ) {
			$this->log_reponse_error( $gist, $response );
		} else {
			$this->log(
				sprintf(
					__( 'Created Repo %s for gist %s', 'wp-gistpen' ),
					$response->ID,
					$gist->id
				),
				Level::SUCCESS
			);
		}

		return null;
	}

	/**
	 * Update the gist with the provided Repo.
	 *
	 * @param Repo     $repo Repo to update.
	 * @param stdClass $gist Response from Gist.
	 *
	 * @return null
	 */
	private function update_repo_for_gist( Repo $repo, stdClass $gist ) {
		$repo->unguard();
		$repo->sync = 'on';
		$repo->description = $gist->description;
		$repo->status = $gist->public === true ? 'publish' : 'private';
		$repo->gist_id = $gist->id;

		$files = (array) $gist->files;
		$blobs = $repo->blobs->filter(function ( Blob $blob ) use ( &$files ) {
			foreach ( $files as $name => $meta ) {
				if ( $name === $blob->filename ) {
					$blob->code = $meta->content;
					$blob->language = $this->em
						->find_by(
							Klass::LANGUAGE,
							array( 'slug' => $this->map_gist_language( $meta->language ) )
						)
						->first();
					unset( $files[ $name ] );
					return true;
				}
			}

			return false;
		} );

		foreach ( $files as $name => $meta ) {
			$blobs = $blobs->add( $this->map_name_and_meta_to_blob( $name, $meta ) );
		}

		$repo->blobs = $blobs;
		$repo->reguard();

		$result = $this->em->persist( $repo );

		if ( is_wp_error( $result ) ) {
			$this->log(
				sprintf(
					__( 'Error saving repo for gist %s. Error: %s', 'wp-gistpen' ),
					$gist->id,
					$result->get_error_message()
				),
				Level::ERROR
			);
		} else {
			$this->log(
				sprintf(
					__( 'Successfully imported gist %s from Gist. Updated with repo id %s.', 'wp-gistpen' ),
					$gist->id,
					$repo->ID
				),
				Level::SUCCESS
			);
		}

		return null;
	}

	/**
	 * Transforms the provided gist data into a Repo.
	 *
	 * @param stdClass $gist
	 *
	 * @return Repo
	 */
	private function map_gist_to_new_entity( stdClass $gist ) {
		$repo = new Repo;

		$repo->unguard();
		$repo->sync = 'on';
		$repo->description = $gist->description;
		$repo->status = $gist->public === true ? 'publish' : 'private';
		$repo->gist_id = $gist->id;

		$blobs = new Collection( Klass::BLOB, array() );

		foreach ( $gist->files as $name => $meta ) {
			$blobs = $blobs->add( $this->map_name_and_meta_to_blob( $name, $meta ) );
		}

		$repo->blobs = $blobs;
		$repo->reguard();

		return $repo;
	}

	/**
	 * Transforms the gist file meta into a Blob.
	 *
	 * @param string   $name
	 * @param stdClass $meta
	 *
	 * @return Blob
	 */
	private function map_name_and_meta_to_blob( $name, stdClass $meta ) {
		$blob = new Blob;

		$blob->filename = $name;
		$blob->code = $meta->content;
		$blob->language = new Language( array(
			'slug' => $this->map_gist_language( $meta->language ),
		) );

		return $blob;
	}

	/**
	 * Maps gist's language to our slugs.
	 *
	 * @param string $language
	 *
	 * @return string
	 */
	private function map_gist_language( $language ) {
		switch ( $language ) {
			case 'JavaScript':
				return 'javascript';
			default:
				return strtolower( $language );
		}
	}

	/**
	 * Log the provided error.
	 *
	 * @param stdClass $gist
	 * @param WP_Error $response
	 */
	private function log_reponse_error( stdClass $gist, WP_Error $response ) {
		$this->log(
			sprintf(
				__( 'Error fetching gist %s. Error: %s', 'wp-gistpen' ),
				$gist->id,
				$response->get_error_message()
			),
		Level::ERROR );

		if ( $response->get_error_code() === 'auth_error' ) {
			$this->log(
				sprintf(
					__( 'Will not reprocess gist %s. Authorization failed. Check that your gist token is valid.', 'wp-gistpen' ),
					$gist->id
				),
			Level::WARNING );
		}

		if ( $response->get_error_code() === 'client_error' ) {
			$this->log(
				sprintf(
					__( 'Will not reprocess gist %s. Client error. Please report to the developer.', 'wp-gistpen' ),
					$gist->id
				),
				Level::WARNING
			);
		}
	}
}
