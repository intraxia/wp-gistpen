<?php
namespace Intraxia\Gistpen\Database;

use Exception;
use Intraxia\Gistpen\Contract\Repository;
use Intraxia\Gistpen\Database\Repository\WordPressPost;
use Intraxia\Gistpen\Database\Repository\WordPressTerm;
use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Model\Commit;
use Intraxia\Gistpen\Model\Language;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Model\State;
use Intraxia\Jaxion\Axolotl\Collection;
use Intraxia\Jaxion\Axolotl\GuardedPropertyException;
use Intraxia\Jaxion\Axolotl\Model;
use Intraxia\Jaxion\Contract\Axolotl\EntityManager as EntityManagerContract;
use WP_Error;

class EntityManager implements EntityManagerContract {
	/**
	 * Model class for the Repo.
	 */
	const REPO_CLASS = 'Intraxia\Gistpen\Model\Repo';

	/**
	 * Model class for the Repo.
	 */
	const BLOB_CLASS = 'Intraxia\Gistpen\Model\Blob';

	/**
	 * Model class for the Repo.
	 */
	const LANGUAGE_CLASS = 'Intraxia\Gistpen\Model\Language';

	/**
	 * Model class for the Commit.
	 */
	const COMMIT_CLASS = 'Intraxia\Gistpen\Model\Commit';

	/**
	 * Model class for the Commit.
	 */
	const STATE_CLASS = 'Intraxia\Gistpen\Model\State';

	/**
	 * Meta prefix.
	 *
	 * @var string
	 */
	protected $prefix;

	/**
	 * Repositories for Model types.
	 *
	 * @var Repository[]
	 */
	protected $repositories;

	/**
	 * EntityManager constructor.
	 *
	 * @param string $prefix Meta prefix for entities.
	 */
	public function __construct( $prefix ) {
		$this->prefix = $prefix;

		$this->repositories = array(
			'Intraxia\Jaxion\Contract\Axolotl\UsesWordPressPost' => new WordPressPost( $this, $this->prefix ),
			'Intraxia\Jaxion\Contract\Axolotl\UsesWordPressTerm' => new WordPressTerm( $this, $this->prefix ),
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string $class Fully qualified class name of model.
	 * @param int    $id    ID of the model.
	 *
	 * @return Model|WP_Error
	 */
	public function find( $class, $id, array $params = array() ) {
		if ( ! is_subclass_of( $class, 'Intraxia\Jaxion\Axolotl\Model' ) ) {
			return new WP_Error( 'Invalid model' );
		}

		foreach ( $this->repositories as $interface => $repository ) {
			if ( is_subclass_of( $class, $interface ) ) {
				return $repository->find( $class, $id, $params );
			}
		}

		return new WP_Error( 'Invalid Model' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string $class  Fully qualified class name of models to find.
	 * @param array  $params Params to constrain the find.
	 *
	 * @return Collection|WP_Error
	 */
	public function find_by( $class, array $params = array() ) {
		if ( ! is_subclass_of( $class, 'Intraxia\Jaxion\Axolotl\Model' ) ) {
			return new WP_Error( 'Invalid model' );
		}

		foreach ( $this->repositories as $interface => $repository ) {
			if ( is_subclass_of( $class, $interface ) ) {
				return $repository->find_by( $class, $params );
			}
		}

		return new WP_Error( 'Invalid Model' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string $class
	 * @param array  $data
	 *
	 * @return Model|WP_Error
	 */
	public function  create( $class, array $data = array(), array $options = array() ) {
		foreach ( $this->repositories as $interface => $repository ) {
			if ( is_subclass_of( $class, $interface ) ) {
				return $repository->create( $class, $data, $options );
			}
		}

		return new WP_Error( 'Invalid Model' );
	}

	/**
	 * @inheritDoc
	 */
	public function persist( Model $model ) {
		if ( $model instanceof Repo ) {
			return $this->persist_repo( $model );
		}

		if ( $model instanceof Blob ) {
			return $this->persist_blob( $model );
		}

		if ( $model instanceof Language ) {
			return $this->persist_language( $model );
		}

		if ( $model instanceof Commit ) {
			return $this->persist_commit( $model );
		}

		if ( $model instanceof State ) {
			return $this->persist_state( $model );
		}

		return new WP_Error( 'Invalid class' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param Model $model
	 * @param bool  $force
	 *
	 * @return WP_Error|mixed
	 */
	public function delete( Model $model, $force = false ) {
		if ( $model instanceof Repo ) {
			return $this->delete_repo( $model, $force );
		}

		if ( $model instanceof Blob ) {
			return $this->delete_blob( $model, $force );
		}

		if ( $model instanceof Language ) {
			return $this->delete_language( $model, $force );
		}

		return new WP_Error( 'Invalid class' );
	}

	/**
	 * Updates a Repo to sync with the database.
	 *
	 * @param Repo $model
	 *
	 * @return Repo|WP_Error
	 */
	protected function persist_repo( Repo $model ) {
		$result  = wp_update_post( $model->get_underlying_wp_object(), true );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		foreach ( $model->get_changed_table_attributes() as $key => $value ) {
			update_post_meta( $model->get_primary_id() , "_{$this->prefix}_{$key}", $value );
		}

		$model->set_attribute( Model::OBJECT_KEY, get_post( $model->get_primary_id() ) );

		$deleted_blobs = $model->get_original_attribute( 'blobs' )
			->filter(function( Blob $original_blob ) use ( &$model ) {
				/** @var Blob $blob */
				foreach ( $model->blobs as $blob ) {
					if ( $blob->get_primary_id() === $original_blob->get_primary_id() ) {
						return false;
					}
				}

				return true;
			});

		/** @var Blob $blob */
		foreach ( $model->blobs as $blob ) {
			$blob->unguard();
			$blob->repo_id = $model->get_primary_id();
			$blob->status = $model->get_attribute( 'status' );
			$blob->reguard();

			$this->persist_blob( $blob );
		}

		/** @var Blob $deleted_blob */
		foreach ( $deleted_blobs as $deleted_blob ) {
			wp_trash_post( $deleted_blob->get_primary_id() );
		}

		return $this->find( static::REPO_CLASS, $model->get_primary_id() );
	}

	/**
	 * Updates a Blob to sync with the database.
	 *
	 * @param Repo $model
	 *
	 * @return Repo|WP_Error
	 */
	protected function persist_blob( Blob $model ) {
		$result  = $model->get_primary_id() ?
			wp_update_post( $model->get_underlying_wp_object(), true ) :
			wp_insert_post( (array) $model->get_underlying_wp_object(), true );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$model->set_attribute( Model::OBJECT_KEY, get_post( $result ) );

		foreach ( $model->get_changed_table_attributes() as $key => $value ) {
			update_post_meta( $model->get_primary_id(), $key, $value );
		}

		try {
			wp_set_object_terms( $model->get_primary_id(), $model->language->slug, Language::get_taxonomy(), false );
		} catch ( Exception $exception ) {
			// @todo what to do?
		}

		return $this->find( static::BLOB_CLASS, $model->get_primary_id() );
	}

	/**
	 * Updates a Language to sync with the database.
	 *
	 * @param Language $model
	 *
	 * @return Language|WP_Error
	 */
	protected function persist_language( Language $model ) {
		$result  = $model->get_primary_id() ?
			wp_update_term(
				$model->get_primary_id(),
				"{$this->prefix}_language",
				(array) $model->get_underlying_wp_object()
			) :
			wp_insert_term( $model->slug, "{$this->prefix}_language" );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$model->set_attribute( Model::OBJECT_KEY, get_term( $result['term_id'] ) );

		foreach ( $model->get_table_attributes() as $key => $attribute ) {
			update_post_meta(
				$model->get_primary_id(),
				$this->make_meta_key( $key ),
				$attribute
			);
		}

		return $this->find( static::LANGUAGE_CLASS, $model->get_primary_id() );
	}

	/**
	 * Updates a Repo to sync with the database.
	 *
	 * @param Commit $model
	 *
	 * @return Commit|WP_Error
	 */
	protected function persist_commit( Commit $model ) {
		$result  = wp_update_post( $model->get_underlying_wp_object(), true );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		foreach ( $model->get_changed_table_attributes() as $key => $value ) {
			update_metadata(
				'post',
				$model->get_primary_id() ,
				"_{$this->prefix}_{$key}",
				$value
			);
		}

		$model->set_attribute( Model::OBJECT_KEY, get_post( $model->get_primary_id() ) );

		return $this->find( static::COMMIT_CLASS, $model->get_primary_id() );
	}

	/**
	 * Updates a Repo to sync with the database.
	 *
	 * @param State $model
	 *
	 * @return State|WP_Error
	 */
	protected function persist_state( State $model ) {
		$result  = wp_update_post( $model->get_underlying_wp_object(), true );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		foreach ( $model->get_changed_table_attributes() as $key => $value ) {
			update_metadata(
				'post',
				$model->get_primary_id() ,
				"_{$this->prefix}_{$key}",
				$value
			);
		}

		$model->set_attribute( Model::OBJECT_KEY, get_post( $model->get_primary_id() ) );

		return $this->find( static::STATE_CLASS, $model->get_primary_id() );
	}

	/**
	 * Deletes the Repo and all its associated Blobs.
	 *
	 * @param Repo $model
	 * @param bool $force
	 *
	 * @return Repo|WP_Error
	 */
	protected function delete_repo( Repo $model, $force ) {
		$id = $model->get_primary_id();

		if ( ! $id ) {
			return new WP_Error( __( 'Repo does not exist in the database.' ) );
		}

		$result = wp_delete_post( $id, $force );

		if ( ! $result ) {
			return new WP_Error( __( 'Failed to delete Repo from the Database.' ) );
		}

		foreach ( $model->blobs as $blob ) {
			$this->delete_blob( $blob, $force );
		}

		return $model;
	}

	/**
	 * Delete a Blob from the database.
	 *
	 * @param Blob $model
	 * @param bool $force
	 *
	 * @return Blob|WP_Error
	 */
	protected function delete_blob( Blob $model, $force ) {
		$id = $model->get_primary_id();

		if ( ! $id ) {
			return new WP_Error( __( 'Repo does not exist in the database.' ) );
		}

		$result = wp_delete_post( $id, $force );

		if ( ! $result ) {
			return new WP_Error( __( 'Failed to delete Repo from the Database.' ) );
		}

		return $model;
	}

	protected function delete_language( Language $model, $force ) {
		return new WP_Error( 'not implemented' );
	}

	/**
	 * Wraps the given key with the string required to make it a meta key.
	 *
	 * @param {string} $key Key to turn into meta key.
	 *
	 * @return string Generated meta key.
	 */
	protected function make_meta_key( $key ) {
		return "_{$this->prefix}_{$key}";
	}
}
