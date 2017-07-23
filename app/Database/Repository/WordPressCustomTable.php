<?php

namespace Intraxia\Gistpen\Database\Repository;

use Intraxia\Gistpen\Model\Klass;
use Intraxia\Jaxion\Axolotl\Model;
use Intraxia\Jaxion\Axolotl\Collection;
use Intraxia\Jaxion\Contract\Axolotl\UsesCustomTable;
use WP_Error;

class WordPressCustomTable extends AbstractRepository {

	/**
	 * Get a single model of the provided class with the given ID.
	 *
	 * @param string $class  Fully qualified class name of model.
	 * @param int    $id     ID of the model.
	 * @param array  $params Extra params/options.
	 *
	 * @return Model|WP_Error
	 */
	public function find( $class, $id, array $params = array() ) {
		global $wpdb;

		$result = $wpdb->get_row(
			$wpdb->prepare(
				"
					SELECT * FROM {$this->em->make_table_name( $class )}
					WHERE {$class::get_primary_key()} = %d
				",
				$id
			),
			ARRAY_A
		);

		if ( ! $result ) {
			return new WP_Error( 'db_error', $wpdb->last_error );
		}

		/** @var Model $model */
		$model = new $class( $result );
		$model->sync_original();

		return $model;
	}

	/**
	 * Finds all the models of the provided class for the given params.
	 *
	 * This method will return an empty Collection if the query returns no models.
	 *
	 * @param string $class  Fully qualified class name of models to find.
	 * @param array  $params Params to constrain the find.
	 *
	 * @return Collection|WP_Error
	 */
	public function find_by( $class, array $params = array() ) {
		global $wpdb;
		$models = new Collection( $class );

		$query = "SELECT * FROM {$this->em->make_table_name( $class )}";

		foreach ( $params as $key => $value ) {
			switch ( $key ) {
				default:
					if ( $this->is_valid_key( $class, $key ) ) {
						$query .= $wpdb->prepare(
							" WHERE {$key} = %s",
							$value
						);
					}
			}
		}

		$results = $wpdb->get_results( $query, ARRAY_A );

		if ( ! is_array( $results )  ) {
			return new WP_Error( 'db_error', $wpdb->last_error );
		}

		foreach ( $results as $result ) {
			/** @var Model $model */
			$model  = new $class( $result );
			$model->sync_original();

			$models = $models->add( $model );
		}

		return $models;
	}

	/**
	 * Saves a new model of the provided class with the given data.
	 *
	 * @param string $class
	 * @param array  $data
	 * @param array  $options
	 *
	 * @return Model|WP_Error
	 */
	public function create( $class, array $data = array(), array $options = array() ) {
		global $wpdb;

		$results = $wpdb->insert(
			$this->em->make_table_name( $class ),
			// @todo escape values
			// this is only acceptable because user input never gets in here right now
			$data
		);

		if ( ! $results ) {
			return new WP_Error( 'db_error', $wpdb->last_error );
		}

		return $this->find( $class, $wpdb->insert_id );
	}

	/**
	 * Updates a model with its latest data.
	 *
	 * @param Model $model
	 *
	 * @return Model|WP_Error
	 */
	public function persist( Model $model ) {
		global $wpdb;

		$class = get_class( $model );

		if ( ! $model->get_primary_id() ) {
			return $this->create( $class, $model->get_table_attributes() );
		}

		$results = $wpdb->update(
			$this->em->make_table_name( $class ),
			$model->get_changed_table_attributes(),
			/** UsesCustomTable $model */
			array( $model->get_primary_key() => $model->get_primary_id() )
		);

		if ( ! $results ) {
			return new WP_Error( 'db_error', $wpdb->last_error );
		}

		return $this->find( $class, $model->get_primary_id() );
	}

	/**
	 * Delete the provided model from the database.
	 *
	 * @param Model $model
	 * @param bool  $force
	 *
	 * @return mixed
	 */
	public function delete( Model $model, $force = false ) {
		global $wpdb;

		$results = $wpdb->delete(
			$this->em->make_table_name( $model ),
			array( $model->get_primary_key() => $model->get_primary_id() )
		);

		if ( ! $results ) {
			return new WP_Error( 'db_error', $wpdb->last_error );
		}

		return $model;
	}

	/**
	 * Checks whether the given key is valid for a given class.
	 *
	 * @param string $class Class to check against.
	 * @param string $key   Key to validate.
	 *
	 * @return bool
	 */
	private function is_valid_key( $class, $key ) {
		switch ( $class ) {
			case Klass::RUN:
				return in_array( $key, array( 'job' ), true );
			default:
				return false;
		}
	}
}
