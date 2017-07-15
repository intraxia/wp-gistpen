<?php

namespace Intraxia\Gistpen\Database\Repository;

use Intraxia\Jaxion\Axolotl\Collection;
use Intraxia\Jaxion\Axolotl\Model;
use Intraxia\Jaxion\Contract\Axolotl\UsesWordPressTerm;
use WP_Error;
use WP_Term_Query;

class WordPressTerm extends AbstractRepository {

	/**
	 * @inheritDoc
	 */
	public function find( $class, $id, array $params = array() ) {
		/** @var UsesWordPressTerm $class */
		$taxonomy = $class::get_taxonomy();
		$term  = get_term( $id, $taxonomy );

		if ( ! $term ) {
			$term = new WP_Error( 'Error getting term' );
		}

		if ( is_wp_error( $term ) ) {
			return $term;
		}

		/** @var Model|UsesWordPressTerm $model */
		$model = new $class( array( Model::OBJECT_KEY => $term ) );
		$table = array();

		foreach ( $model->get_table_keys() as $key ) {
			$table[ $key ] = get_term_meta( $term->term_id, $this->make_meta_key( $key ), true );
		}

		$model->set_attribute( Model::TABLE_KEY, $table );

		return $this->fill_relations( $model, $params );
	}

	/**
	 * @inheritDoc
	 */
	public function find_by( $class, array $params = array() ) {
		/** @var UsesWordPressTerm $class */
		$taxonomy = $class::get_taxonomy();
		$collection = new Collection( $class );

		$query = new WP_Term_Query( array_merge( $params, array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'fields'     => 'ids',
		) ) );

		/** WP_Term $term */
		foreach ( $query->get_terms() as $id ) {
			$model = $this->find( $class, $id );

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
