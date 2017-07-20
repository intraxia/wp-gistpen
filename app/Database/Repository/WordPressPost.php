<?php

namespace Intraxia\Gistpen\Database\Repository;

use Exception;
use Intraxia\Gistpen\Database\EntityManager;
use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Model\Commit;
use Intraxia\Gistpen\Model\Language;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Model\State;
use Intraxia\Jaxion\Axolotl\Collection;
use Intraxia\Jaxion\Axolotl\GuardedPropertyException;
use Intraxia\Jaxion\Axolotl\Model;
use Intraxia\Jaxion\Axolotl\PropertyDoesNotExistException;
use Intraxia\Jaxion\Contract\Axolotl\UsesWordPressPost;
use WP_Error;
use WP_Query;

class WordPressPost extends AbstractRepository {

	/**
	 * {@inheritDoc}
	 *
	 * @param string $class
	 * @param int    $id
	 * @param array  $params
	 *
	 * @return WP_Error|Model
	 */
	public function find( $class, $id, array $params = array() ) {
		/** @var UsesWordPressPost $class */
		$post_type = $class::get_post_type();
		$post      = get_post( $id );

		if ( ! $post || $post->post_type !== $post_type ) {
			return new WP_Error( 'Invalid id' );
		}

		if ( $class === EntityManager::BLOB_CLASS && $post->post_parent === 0 ) {
			return new WP_Error( 'Invalid id' );
		}

		/** @var UsesWordPressPost|Model $model */
		$model = new $class( array( Model::OBJECT_KEY => $post ) );
		$table = array();

		foreach ( $model->get_table_keys() as $key ) {
			if ( 'states' === $key ) {
				$table[ $key ] = new Collection( EntityManager::STATE_CLASS );
			}

			// @todo handle related keys specially for now.
			if ( in_array( $key, array( 'blobs', 'language', 'states' ) ) ) {
				continue;
			}

			$value = $table[ $key ] = get_post_meta( $id, $this->make_meta_key( $key ), true );

			// @todo enable custom getter/setter in models
			if ( $key === 'sync' && ! $value ) {
				$table[ $key ] = 'off';
			}

			// Fallback for legacy metadata
			// @todo move to migration
			if ( $key === 'state_ids' ) {
				$value = get_post_meta( $id, '_wpgp_commit_meta', true );

				if ( is_array( $value ) && isset( $value['state_ids'] ) ) {
					$model->set_attribute(
						$key,
						$value['state_ids']
					);

					delete_metadata( 'post', $id, '_wpgp_commit_meta' . true );
				}
			}
		}

		$model->set_attribute( Model::TABLE_KEY, $table );

		return $this->fill_relations( $model, $params );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string $class
	 * @param array  $params
	 *
	 * @return Collection
	 */
	public function find_by( $class, array $params = array() ) {
		/** @var UsesWordPressPost $class */
		$post_type     = $class::get_post_type();
		$parent_search = 'post_parent__in';

		if ( $class === EntityManager::BLOB_CLASS ) {
			$parent_search = 'post_parent__not_in';
		}

		$query_args = array(
			'post_type'    => $post_type,
			$parent_search => array( 0 ),
			'fields'       => 'ids',
		);

		if ( $class === EntityManager::COMMIT_CLASS ) {
			$query_args['post_parent'] = $params['repo_id'];
		}

		if ( $class === EntityManager::STATE_CLASS ) {
			$query_args['post_parent'] = $params['blob_id'];
		}

		$collection = new Collection( $class );
		$query      = new WP_Query( array_merge( $params, $query_args ) );

		foreach ( $query->get_posts() as $id ) {
			$model = $this->find( $class, $id, $params );

			if ( ! is_wp_error( $model ) ) {
				$collection = $collection->add( $model );
			}
		}

		return $collection;
	}

	/**
	 * @inheritDoc
	 */
	public function create( $class, array $data = array(), array $options = array() ) {
		/** @var Model $model */
		$model = new $class;

		/**
		 * Set aside the `blobs` key for use.
		 */
		if ( isset( $data['blobs'] ) ) {
			if ( is_array( $data['blobs'] ) ) {
				$blobs_data = $data['blobs'];
			}

			unset( $data['blobs'] );
		}

		/**
		 * Set aside the `language` key for use.
		 */
		if ( isset( $data['language'] ) ) {
			if ( is_array( $data['language'] ) ) {
				$language_data = $data['language'];
			}

			unset( $data['language'] );
		}

		$unguarded = isset( $options['unguarded'] ) && $options['unguarded'];

		if ( $unguarded ) {
			$model->unguard();
		}

		foreach ( $data as $key => $value ) {
			$model->set_attribute( $key, $value );
		}

		if ( $unguarded ) {
			$model->reguard();
		}

		$result = wp_insert_post( (array) $model->get_underlying_wp_object(), true );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$model->set_attribute( Model::OBJECT_KEY, get_post( $result ) );

		foreach ( $model->get_table_attributes() as $key => $attribute ) {
			$result = update_metadata(
				'post',
				$model->get_primary_id(),
				$this->make_meta_key( $key ),
				$attribute
			);
		}

		if ( isset( $blobs_data ) ) {
			$blobs = new Collection( EntityManager::BLOB_CLASS );

			foreach ( $blobs_data as $blob_data ) {
				$blob_data['repo_id'] = $model->get_primary_id();
				$blob_data['status']  = $model->get_attribute( 'status' );

				$blob = $this->em->create( EntityManager::BLOB_CLASS, $blob_data, array(
					'unguarded' => true,
				) );

				if ( ! is_wp_error( $blob ) ) {
					$blobs = $blobs->add( $blob );
				}
			}

			$model->set_attribute( 'blobs', $blobs );
		}

		if ( isset( $language_data ) ) {
			$language = $this->em->find_by( EntityManager::LANGUAGE_CLASS, array( 'slug' => $language_data['slug'] ) );

			if ( count( $language ) === 0 ) {
				$language = $this->em->create( EntityManager::LANGUAGE_CLASS, $language_data );

				if ( is_wp_error( $language ) ) {
					return $language;
				}
			} else {
				$language = $language->first();
			}

			$model->set_attribute( 'language', $language );

			wp_set_object_terms( $model->get_primary_id(), $model->language->slug, Language::get_taxonomy(), false );
		}

		return $model;
	}

	/**
	 * @inheritDoc
	 */
	public function persist( Model $model ) {
		$result = $model->get_primary_id() ?
			wp_update_post( $model->get_underlying_wp_object(), true ) :
			wp_insert_post( (array) $model->get_underlying_wp_object(), true );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$model->set_attribute( Model::OBJECT_KEY, get_post( $result ) );

		foreach ( $model->get_table_attributes() as $key => $value ) {
			if ( in_array( $key, array( 'blobs', 'language', 'repo' ) ) ) {
				continue;
			}

			if ( $model->get_original_attribute( $key ) !== $value ) {
				update_metadata(
					'post',
					$model->get_primary_id(),
					"_{$this->prefix}_{$key}",
					$value
				);
			}
		}

		// Handle blobs
		if ( $model instanceof Repo ) {
			$deleted_blobs = $model->get_original_attribute( 'blobs' )
				->filter( function ( Model $original_blob ) use ( &$model ) {
					/** @var Model $blob */
					foreach ( $model->blobs as $blob ) {
						if ( $blob->get_primary_id() === $original_blob->get_primary_id() ) {
							return false;
						}
					}

					return true;
				} );

			/** @var Model $blob */
			foreach ( $model->blobs as $blob ) {
				$blob->unguard();
				$blob->repo_id = $model->get_primary_id();
				$blob->status  = $model->get_attribute( 'status' );
				$blob->reguard();

				$this->em->persist( $blob );
			}

			/** @var Model $deleted_blob */
			foreach ( $deleted_blobs as $deleted_blob ) {
				wp_trash_post( $deleted_blob->get_primary_id() );
			}
		}

		if ( $model instanceof Blob ) {
			wp_set_object_terms(
				$model->get_primary_id(),
				$model->language->slug,
				Language::get_taxonomy(),
				false
			);
		}

		if ( $model instanceof Commit && $model->states ) {
			$states = new Collection( EntityManager::STATE_CLASS );

			foreach ( $model->states as $state ) {
				$state = $this->em->persist( $state );

				if ( ! is_wp_error( $state ) ) {
					$states = $states->add( $state );
				}
			}

			$state_ids = $states->map(function (State $state ) {
				return $state->ID;
			} )->to_array();

			update_metadata(
				'post',
				$model->get_primary_id(),
				"_{$this->prefix}_state_ids",
				$state_ids
			);
		}

		return $this->find( get_class( $model ), $model->get_primary_id() );
	}

	/**
	 * @inheritDoc
	 */
	public function delete( Model $model, $force = false ) {
		$id = $model->get_primary_id();

		if ( ! $id ) {
			return new WP_Error( __( 'Repo does not exist in the database.' ) );
		}

		$result = wp_delete_post( $id, $force );

		if ( ! $result ) {
			return new WP_Error( __( 'Failed to delete Repo from the Database.' ) );
		}

		if ( $model instanceof Repo ) {
			foreach ( $model->blobs as $blob ) {
				$this->em->delete( $blob, $force );
			}
		}

		return $model;
	}
}
