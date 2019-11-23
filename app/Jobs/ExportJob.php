<?php
namespace Intraxia\Gistpen\Jobs;

use Intraxia\Gistpen\Client\Gist;
use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Model\Klass;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Model\State;
use Intraxia\Jaxion\Contract\Axolotl\Collection;
use Intraxia\Jaxion\Contract\Axolotl\EntityManager;
use WP_Error;

/**
 * Class ExportJob
 *
 * @package    Intraxia\Gistpen
 * @subpackage Jobs
 */
class ExportJob extends AbstractJob {
	/**
	 * Export target client.
	 *
	 * @var Gist
	 */
	private $client;

	/**
	 * ExportJob constructor.
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
		return 'Export';
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string
	 */
	protected function slug() {
		return 'export';
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string
	 */
	protected function description() {
		return __( 'Export all unexported gistpen repos.', 'wp-gistpen' );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return Collection|WP_Error
	 */
	protected function fetch_items() {
		return $this->em->find_by( Klass::REPO, array(
			'nopaging' => true,
		) );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param Repo $repo
	 *
	 * @return null|Repo
	 */
	protected function process_item( $repo ) {
		if ( ! ( $repo instanceof Repo ) ) {
			$this->log(
				sprintf(
					__( 'Expected to see instance of Repo, got %s instead.', 'wp-gistpen' ),
					gettype( $repo )
				),
				Level::ERROR
			);

			return null;
		}

		$repo = $this->em->find( Klass::REPO, $repo->ID, array(
			'with' => array(
				'blobs' => array(
					'with' => 'language',
				),
			),
		) );

		if ( ! $repo->gist_id ) {
			return $this->create_gist_for_repo( $repo );
		}

		$response = $this->client->one( $repo->gist_id );

		if ( is_wp_error( $response ) ) {
			$this->log(
				sprintf(
					__( 'Error fetching gist for Repo %s. Error: %s', 'wp-gistpen' ),
					$repo->ID,
					$response->get_error_message()
				),
				Level::ERROR
			);

			return null;
		}

		return $this->update_gist_for_repo( $repo, $response->json );
	}

	/**
	 * Creates a new Gist for the provided Repo.
	 *
	 * @param Repo $repo
	 *
	 * @return null
	 */
	private function create_gist_for_repo( Repo $repo ) {
		$response = $this->client->create( $this->map_repo_to_new_entity( $repo ) );

		if ( is_wp_error( $response ) ) {
			$this->log_reponse_error( $repo, $response );

			return null;
		}

		$repo->unguard();
		$repo->gist_id = $response->json->id;
		$repo->sync    = 'on';
		$repo->reguard();

		$repo = $this->em->persist( $repo );

		if ( is_wp_error( $repo ) ) {
			$this->log(
				sprintf(
					__( 'Error saving gist_id for Repo %s. Error: %s', 'wp-gistpen' ),
					$repo->ID,
					$repo->get_error_message()
				),
				Level::ERROR
			);

			return null;
		}

		$this->log(
			sprintf(
				__( 'Successfully exported Repo %s to Gist. Created with gist id %s.', 'wp-gistpen' ),
				$repo->ID,
				$repo->gist_id
			),
		Level::SUCCESS );

		return null;
	}

	/**
	 * Update the gist with the provided Repo.
	 *
	 * @param Repo     $repo
	 * @param stdClass $gist
	 *
	 * @return null
	 */
	private function update_gist_for_repo( Repo $repo, stdClass $gist ) {
		$entity = $this->map_repo_to_new_entity( $repo );

		if ( $this->entity_matches_gist( $entity, $gist ) ) {
			$this->log(
				sprintf(
					__( 'Repo ID %s will not be exported. No changes.', 'wp-gistpen' ),
					$repo->ID
				)
			);

			return null;
		}

		$files      = array();
		$gist_files = (array) $gist->files;

		foreach ( $repo->blobs as $blob ) {
			$states = $this->em->find_by( Klass::STATE, array(
				'blob_id'        => $blob->ID,
				'posts_per_page' => 2,
				'order'          => 'DESC',
				'orderby'        => 'ID',
			) );

			$current_state = $states->first();
			$previous_state = $states->last();

			$file = array();

			if ( $current_state->filename !== $previous_state->filename ) {
				$file['filename'] = $current_state->filename;
			}

			if ( $current_state->code !== $previous_state->code ) {
				$file['content'] = $current_state->code;
			}

			if ( $file ) {
				$files[ $previous_state->filename ] = $file;
			}

			if ( isset( $gist_files[ $previous_state->filename ] ) ) {
				unset( $gist_files[ $previous_state->filename ] );
			}
		}

		// Delete remaining files.
		foreach ( array_keys( $gist_files ) as $filename ) {
			$files[ $filename ] = null;
		}

		$entity['files'] = $files;

		$response = $this->client->update( $repo->gist_id, $entity );

		if ( is_wp_error( $response ) ) {
			$this->log_reponse_error( $repo, $response );

			return null;
		}

		$this->log(
			sprintf(
				__( 'Successfully updated Repo ID %s', 'wp-gistpen' ),
				$repo->ID
			),
			Level::SUCCESS
		);

		return null;
	}

	/**
	 * Create a new gist entity from the provided Repo.
	 *
	 * @param Repo $repo
	 *
	 * @return array
	 */
	private function map_repo_to_new_entity( Repo $repo ) {
		$files = array();

		foreach ( $repo->blobs as $blob ) {
			$files[ $blob->filename ] = array(
				'content' => $blob->code,
			);
		}

		return array(
			'description' => $repo->description,
			'public'      => $repo->status === 'publish',
			'files'       => $files,
		);
	}

	/**
	 * Determines whether the provided entity matches the provided gist.
	 *
	 * @param array     $entity
	 * @param \stdClass $gist
	 *
	 * @return bool
	 */
	private function entity_matches_gist( $entity, $gist ) {
		if ( $entity['description'] !== $gist->description ) {
			return false;
		}

		if ( $entity['public'] !== $gist->public ) {
			return false;
		}

		$files = (array) $gist->files;

		if ( count( $entity['files'] ) !== count( $files ) ) {
			return false;
		}

		foreach ( $files as $filename => $value ) {
			if ( ! isset( $entity['files'][ $filename ] ) ) {
				return false;
			}

			if ( $entity['files'][ $filename ]['content'] !== $value->content ) {
				return false;
			}
		}

		foreach ( $entity['files'] as $filename => $value ) {
			if ( ! isset( $files[ $filename ] ) ) {
				return false;
			}

			if ( $files[ $filename ]->content !== $value['content'] ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Log the provided error.
	 *
	 * @param Repo     $repo
	 * @param WP_Error $response
	 */
	private function log_reponse_error( Repo $repo, WP_Error $response ) {
		$this->log(
			sprintf(
				__( 'Error creating new gist for Repo %s. Error: %s', 'wp-gistpen' ),
				$repo->ID,
				$response->get_error_message()
			),
			Level::ERROR
		);

		if ( $response->get_error_code() === 'auth_error' ) {
			$this->log(
				sprintf(
					__( 'Will not reprocess Repo %s. Authorization failed. Check that your gist token is valid.', 'wp-gistpen' ),
					$repo->ID
				),
				Level::WARNING
			);
		}

		if ( $response->get_error_code() === 'client_error' ) {
			$this->log(
				sprintf(
					__( 'Will not reprocess Repo %s. Client error. Please report to the developer.', 'wp-gistpen' ),
					$repo->ID
				),
				Level::WARNING
			);
		}
	}
}
