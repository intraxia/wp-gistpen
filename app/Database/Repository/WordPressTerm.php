<?php

namespace Intraxia\Gistpen\Database\Repository;

use Intraxia\Gistpen\Model\Language;
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
	 * {@inheritdoc}
	 *
	 * @param string $class
	 * @param array  $data
	 * @param array  $options
	 *
	 * @return Model|WP_Error
	 */
	public function create( $class, array $data = array(), array $options = array() ) {
		/** @var Model $model */
		$model = new $class;

		foreach ( $data as $key => $value ) {
			$model->set_attribute( $key, $value );
		}
		/** @var UsesWordPressTerm $class */
		$taxonomy = $class::get_taxonomy();
		$result   = wp_insert_term( $model->slug, $taxonomy );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$model->set_attribute( Model::OBJECT_KEY, get_term( $result['term_id'] ) );

		foreach ( $model->get_table_attributes() as $key => $attribute ) {
			update_term_meta(
				$model->get_primary_id(),
				$this->make_meta_key( $key ),
				$attribute
			);
		}

		return $model;
	}

	/**
	 * @inheritDoc
	 */
	public function persist( Model $model ) {
		$result  = $model->get_primary_id() ?
			wp_update_term(
				$model->get_primary_id(),
				Language::get_taxonomy(),
				(array) $model->get_underlying_wp_object()
			) :
			wp_insert_term( $model->slug, "{$this->prefix}_language" );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$model->set_attribute( Model::OBJECT_KEY, get_term( $result['term_id'] ) );

		foreach ( $model->get_table_attributes() as $key => $value ) {
			if ( $model->get_original_attribute( $key ) !== $value ) {
				update_metadata(
					'term',
					$model->get_primary_id(),
					$this->make_meta_key( $key ),
					$value
				);
			}
		}

		return $this->find( get_class( $model ), $model->get_primary_id() );
	}

	/**
	 * @inheritDoc
	 */
	public function delete( Model $model, $force = false ) {
		return new WP_Error( 'not implemented' );
	}
}
