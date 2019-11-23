<?php

namespace Intraxia\Gistpen\Database\Repository;

use Intraxia\Gistpen\Model\Klass;
use Intraxia\Gistpen\Model\Run;
use Intraxia\Jaxion\Axolotl\Model;
use Intraxia\Jaxion\Axolotl\Collection;
use Intraxia\Jaxion\Contract\Axolotl\UsesCustomTable;
use WP_Error;

/**
 * Service for managing Models backed by a custom table.
 */
class WordPressCustomTable extends AbstractRepository {

	/**
	 * Get a single model of the provided class with the given ID.
	 *
	 * @param string $class  Fully qualified class name of model.
	 * @param int    $id     ID of the model.
	 * @param array  $params Extra params/options.
	 *
	 * @return Model|UsesCustomTable|WP_Error
	 */
	public function find( $class, $id, array $params = array() ) {
		global $wpdb;

		$result = $wpdb->get_row(
			$wpdb->prepare(
				// @codingStandardsIgnoreStart
				"
					SELECT * FROM {$this->em->make_table_name( $class )}
					WHERE {$class::get_primary_key()} = %d
				",
				// @codingStandardsIgnoreEnd
				$id
			),
			ARRAY_A
		);

		if ( ! $result ) {
			return new WP_Error(
				'db_error',
				sprintf(
					__( 'Query failed with error: %s', 'wp-gistpen' ),
					$wpdb->last_error
				)
			);
		}

		if ( isset( $result['items'] ) ) {
			$result['items'] = maybe_unserialize( $result['items'] );
		}

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
				case 'order_by':
					// skip, handled below
					break;
				default:
					if ( $this->is_valid_key( $class, $key ) ) {
						$query .= $wpdb->prepare(
							" WHERE {$key} = %s",  // @codingStandardsIgnoreLine
							$value
						);
					}
			}
		}

		if ( isset( $params['order_by'] ) ) {
			// @todo find a better way of whitelisting order_by
			switch ( $params['order_by'] ) {
				case 'ID':
					$query .= " ORDER BY {$params['order_by']} DESC";
					break;
			}
		}

		$results = $wpdb->get_results( $query, ARRAY_A );  // @codingStandardsIgnoreLine

		if ( ! is_array( $results ) ) {
			return new WP_Error(
				'db_error',
				sprintf(
					__( 'Query failed with error: %s', 'wp-gistpen' ),
					$wpdb->last_error
				)
			);
		}

		foreach ( $results as $result ) {
			if ( isset( $result['items'] ) ) {
				$result['items'] = maybe_unserialize( $result['items'] );
			}

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

		$data = $this->validate_data( $class, $data );

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$results = $wpdb->insert(
			$this->em->make_table_name( $class ),
			// @todo escape values
			// this is only acceptable because user input never gets in here right now
			$data
		);

		if ( ! $results ) {
			return new WP_Error(
				'db_error',
				sprintf(
					__( 'Query failed with error: %s', 'wp-gistpen' ),
					$wpdb->last_error
				)
			);
		}

		return $this->find( $class, $wpdb->insert_id );
	}

	/**
	 * Updates a model with its latest data.
	 *
	 * @param Model|UsesCustomTable $model
	 *
	 * @return Model|WP_Error
	 */
	public function persist( Model $model ) {
		global $wpdb;

		$class = get_class( $model );

		if ( ! $model->get_primary_id() ) {
			return $this->create( $class, $model->get_table_attributes() );
		}

		$valid = $this->validate_data( $class, $model->get_table_attributes() );

		if ( is_wp_error( $valid ) ) {
			return $valid;
		}

		$results = $wpdb->update(
			$this->em->make_table_name( $class ),
			$valid,
			// @todo only update changed attributes
			/** UsesCustomTable $model */
			array( $model->get_primary_key() => $model->get_primary_id() )
		);

		if ( ! $results ) {
			return new WP_Error(
				'db_error',
				sprintf(
					__( 'Query failed with error: %s', 'wp-gistpen' ),
					$wpdb->last_error
				)
			);
		}

		return $this->find( $class, $model->get_primary_id() );
	}

	/**
	 * Delete the provided model from the database.
	 *
	 * @param Model|UsesCustomTable $model
	 * @param bool                  $force
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
			return new WP_Error(
				'db_error',
				sprintf(
					__( 'Query failed with error: %s', 'wp-gistpen' ),
					$wpdb->last_error
				)
			);
		}

		if ( $model instanceof Run ) {
			$results = $wpdb->delete(
				$this->em->make_table_name( Klass::MESSAGE ),
				array( 'run_id' => $model->get_primary_id() )
			);

			if ( ! $results ) {
				return new WP_Error(
					'db_error',
					sprintf(
						__( 'Query failed with error: %s', 'wp-gistpen' ),
						$wpdb->last_error
					)
				);
			}
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
			case Klass::MESSAGE:
				return in_array( $key, array( 'run_id' ), true );
			case Klass::RUN:
				return in_array( $key, array( 'job', 'status' ), true );
			default:
				return false;
		}
	}

	/**
	 * Validate a classes array of data.
	 *
	 * @param string $class
	 * @param array  $data
	 *
	 * @return WP_Error|array
	 */
	private function validate_data( $class, $data ) {
		switch ( $class ) {
			case Klass::RUN:
				if ( isset( $data['items'] ) ) {
					$data['items'] = maybe_serialize( $data['items'] );
				}

				return $data;
			case Klass::MESSAGE:
				if ( ! isset( $data['run_id'] ) ) {
					return new WP_Error(
						'invalid_run_id',
						__( 'run_id was not provided' )
					);
				}

				global $wpdb;

				$count = (int) $wpdb->get_var(
					$wpdb->prepare(
						 // @codingStandardsIgnoreStart
						"
							SELECT COUNT(*) FROM {$this->em->make_table_name( Klass::RUN )}
							WHERE ID = %d
						",
						 // @codingStandardsIgnoreEnd
						$data['run_id']
					)
				);

				if ( ! $count ) {
					return new WP_Error(
						'invalid_data',
						sprintf(
							__( 'run_id %s is invalid', 'wp-gistpen' ),
							$data['run_id']
						)
					);
				}

				return $data;
			default:
				return $data;
		}
	}
}
