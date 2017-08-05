<?php

namespace Intraxia\Gistpen\Database;

use Intraxia\Gistpen\Contract\Repository;
use Intraxia\Gistpen\Database\Repository\WordPressCustomTable;
use Intraxia\Gistpen\Database\Repository\WordPressPost;
use Intraxia\Gistpen\Database\Repository\WordPressTerm;
use Intraxia\Gistpen\Model\Klass;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Jaxion\Axolotl\Collection;
use Intraxia\Jaxion\Axolotl\Model;
use Intraxia\Jaxion\Contract\Axolotl\EntityManager as EntityManagerContract;
use Intraxia\Jaxion\Contract\Axolotl\UsesCustomTable;
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
			'Intraxia\Jaxion\Contract\Axolotl\UsesCustomTable'   => new WordPressCustomTable( $this, $this->prefix ),
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
				$model = $repository->find( $class, $id, $params );

				if ( is_wp_error( $model ) ) {
					return $model;
				}

				do_action( "{$this->prefix}.find.{$this->get_name( $class )}", $model );

				return $model;
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
				$collection = $repository->find_by( $class, $params );

				if ( is_wp_error( $collection ) ) {
					return $collection;
				}

				do_action( "{$this->prefix}.find_by.{$this->get_name( $class )}", $collection );

				return $collection;
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
	public function create( $class, array $data = array(), array $options = array() ) {
		foreach ( $this->repositories as $interface => $repository ) {
			if ( is_subclass_of( $class, $interface ) ) {
				$model = $repository->create( $class, $data, $options );

				if ( is_wp_error( $model ) ) {
					return $model;
				}

				if ( $model instanceof Repo ) {
					$model = $this->find( self::REPO_CLASS, $model->ID, array(
						'with' => array(
							'blobs' => array(
								'with' => 'language',
							),
						),
					) );
				}

				do_action( "{$this->prefix}.create.{$this->get_name( $class )}", $model );

				return $model;
			}
		}

		return new WP_Error( 'Invalid Model' );
	}

	/**
	 * @inheritDoc
	 */
	public function persist( Model $model ) {
		foreach ( $this->repositories as $interface => $repository ) {
			if ( is_subclass_of( $model, $interface ) ) {
				$model = $repository->persist( $model );

				if ( is_wp_error( $model ) ) {
					return $model;
				}

				if ( $model instanceof Repo ) {
					$model = $this->find( self::REPO_CLASS, $model->ID, array(
						'with' => array(
							'blobs' => array(
								'with' => 'language',
							),
						),
					) );
				}

				do_action( "{$this->prefix}.persist.{$this->get_name( $model )}", $model );

				return $model;
			}
		}

		return new WP_Error( 'Invalid class' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param Model $model
	 * @param bool  $force
	 *
	 * @return WP_Error|Model
	 */
	public function delete( Model $model, $force = false ) {
		foreach ( $this->repositories as $interface => $repository ) {
			if ( is_subclass_of( $model, $interface ) ) {
				$model = $repository->delete( $model, $force );

				if ( is_wp_error( $model ) ) {
					return $model;
				}

				do_action( "{$this->prefix}.delete.{$this->get_name( $model )}", $model );

				return $model;
			}
		}

		return new WP_Error( 'Invalid class' );
	}

	/**
	 * Combines the relevant prefixes to make a table name for a given class.
	 *
	 * @param UsesCustomTable|string $class
	 *
	 * @return string
	 */
	public function make_table_name( $class ) {
		global $wpdb;

		return $wpdb->prefix . $this->prefix . '_' . $class::get_table_name();
	}

	/**
	 * Gets the simplified name of the given class.
	 *
	 * @param string|object $class
	 *
	 * @return string
	 */
	private function get_name( $class ) {
		if ( is_object( $class ) ) {
			$class = get_class( $class );
		}

		$name = explode( '\\', $class );
		$name = strtolower( array_pop( $name ) );

		return $name;
	}
}
