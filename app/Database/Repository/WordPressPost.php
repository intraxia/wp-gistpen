<?php
namespace Intraxia\Gistpen\Database\Repository;

use Intraxia\Gistpen\Database\EntityManager;
use Intraxia\Jaxion\Axolotl\Collection;
use Intraxia\Jaxion\Axolotl\Model;
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
		$post  = get_post( $id );

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
				$value = get_post_meta( $id, '_wpgp_commit_meta', true);

				if ( is_array( $value ) && isset( $value['state_ids'] ) ) {
					$model->set_attribute(
						$key,
						$value['state_ids']
					);

					delete_metadata( 'post', $id, '_wpgp_commit_meta'. true );
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
		$post_type = $class::get_post_type();
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
		$query = new WP_Query( array_merge( $params, $query_args ) );

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
	public function create( $class, array $data = array() ) {
		// TODO: Implement create() method.
	}

	/**
	 * @inheritDoc
	 */
	public function persist( Model $model ) {
		// TODO: Implement persist() method.
	}

	/**
	 * @inheritDoc
	 */
	public function delete( Model $model, $force = false ) {
		// TODO: Implement delete() method.
	}
}
