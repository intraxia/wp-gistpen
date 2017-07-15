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
		foreach ( $this->repositories as $interface => $repository ) {
			if ( is_subclass_of( $model, $interface ) ) {
				return $repository->persist( $model );
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
	 * @return WP_Error|mixed
	 */
	public function delete( Model $model, $force = false ) {
		foreach ( $this->repositories as $interface => $repository ) {
			if ( is_subclass_of( $model, $interface ) ) {
				return $repository->delete( $model, $force );
			}
		}

		return new WP_Error( 'Invalid class' );
	}
}
