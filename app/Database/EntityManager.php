<?php
namespace Intraxia\Gistpen\Database;

use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Model\Language;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Jaxion\Axolotl\Collection;
use Intraxia\Jaxion\Axolotl\GuardedPropertyException;
use Intraxia\Jaxion\Axolotl\Model;
use Intraxia\Jaxion\Contract\Axolotl\EntityManager as EntityManagerContract;
use stdClass;
use WP_Error;
use WP_Query;
use WP_Term;

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
	 * Meta prefix.
	 *
	 * @var string
	 */
	protected $prefix;

	/**
	 * Internal Model cache.
	 *
	 * @var array
	 */
	private $cache = array(
		self::REPO_CLASS     => array(),
		self::BLOB_CLASS     => array(),
		self::LANGUAGE_CLASS => array(),
	);

	/**
	 * EntityManager constructor.
	 *
	 * @param string $prefix Meta prefix for entities.
	 */
	public function __construct( $prefix ) {
		$this->prefix = $prefix;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string $class Fully qualified class name of model.
	 * @param int    $id    ID of the model.
	 *
	 * @return Model|WP_Error
	 */
	public function find( $class, $id ) {
		if ( static::REPO_CLASS === $class ) {
			return $this->find_repo( $id );
		}

		if ( static::BLOB_CLASS === $class ) {
			return $this->find_blob( $id );
		}

		if ( static::LANGUAGE_CLASS === $class ) {
			return $this->find_language( $id );
		}

		return new WP_Error( 'Invalid class' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string $class  Fully qualified class name of models to find.
	 * @param array  $params Params to constrain the find.
	 *
	 * @return Collection|WP_Error
	 */
	public function find_by( $class, $params = array() ) {
		if ( static::REPO_CLASS === $class ) {
			return $this->find_repos_by( $params );
		}

		if ( static::BLOB_CLASS === $class ) {
			return $this->find_blobs_by( $params );
		}

		if ( static::LANGUAGE_CLASS === $class ) {
			return $this->find_languages_by( $params );
		}

		return new WP_Error( 'Invalid class' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string $class
	 * @param array  $data
	 *
	 * @return Model|WP_Error
	 */
	public function create( $class, $data = array() ) {
		if ( static::REPO_CLASS === $class ) {
			return $this->create_repo( $data );
		}

		if ( static::BLOB_CLASS === $class ) {
			return $this->create_blob( $data );
		}

		if ( static::LANGUAGE_CLASS === $class ) {
			return $this->create_language( $data );
		}

		return new WP_Error( 'Invalid class' );
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
	 * Fetch a repo by its ID.
	 *
	 * @param {int} $id
	 *
	 * @return Repo|WP_Error
	 */
	protected function find_repo( $id ) {
		if ( isset( $this->cache[ self::REPO_CLASS ][ $id ] ) ) {
			return $this->cache[ self::REPO_CLASS ][ $id ];
		}

		$post  = get_post( $id );
		$model = new Repo;

		if ( $post->post_type !== $model::get_post_type() ) {
			return new WP_Error( 'Invalid id' );
		}

		$model->set_attribute( Model::OBJECT_KEY, $post );

		$this->cache[ self::REPO_CLASS ][ $id ] = $model;

		$model->unguard();

		foreach ( $model->get_table_keys() as $key ) {
			if ( 'blobs' === $key ) {
				$model->set_attribute(
					$key,
					$blobs = $this->find_blobs_by( array(
						'post_parent' => $id,
						'post_status' => $post->post_status,
						'order'       => 'ASC',
						'orderby'     => 'date',
					) )
				);
				continue;
			}

			$model->set_attribute(
				$key,
				get_post_meta( $id, $this->make_meta_key( $key ), true )
			);
		}

		$model->reguard();
		$model->sync_original();

		unset( $this->cache[ self::REPO_CLASS ][ $id ] );

		return $model;
	}

	/**
	 * Fetch a blob by its ID.
	 *
	 * @param {int} $id
	 *
	 * @return Blob|WP_Error
	 */
	protected function find_blob( $id ) {
		$post  = get_post( $id );
		$model = new Blob;

		if ( $post->post_type !== $model::get_post_type() ||
		     $post->post_parent === 0
		) {
			return new WP_Error( 'Invalid id' );
		}

		$model->set_attribute( Model::OBJECT_KEY, $post );
		$model->unguard();

		foreach ( $model->get_table_keys() as $key ) {
			switch ( $key ) {
				case 'repo':
					$model->set_attribute(
						'repo',
						$this->find_repo( $post->post_parent )
					);
					break;
				case 'language':
					$terms = get_the_terms( $post->ID, 'wpgp_language' );

					if ( $terms ) {
						$term = array_pop( $terms );
					} else {
						$term       = new WP_Term( new stdClass );
						$term->slug = 'none';
					}

					$model->set_attribute(
						'language',
						new Language( array( Model::OBJECT_KEY => $term ) )
					);
					break;
				default:
					$model->set_attribute(
						$key,
						get_post_meta( $post->ID, $this->make_meta_key( $key ), true )
					);
					break;
			}
		}

		$model->reguard();
		$model->sync_original();

		return $model;
	}

	/**
	 * Fetch a language by its ID.
	 *
	 * @param {int} $id
	 *
	 * @return Language|WP_Error
	 */
	protected function find_language( $id ) {
		$model = new Language;
		$term  = get_term( $id, $model::get_taxonomy() );

		if ( ! $term ) {
			$term = new WP_Error( 'Error getting term' );
		}

		if ( is_wp_error( $term ) ) {
			return $term;
		}

		$model->set_attribute( Model::OBJECT_KEY, $term );
		$model->unguard();

		foreach ( $model->get_table_keys() as $key ) {
			switch ( $key ) {
				default:
					$model->set_attribute(
						$key,
						get_term_meta( $term->term_id, $this->make_meta_key( $key ), true )
					);
					break;
			}
		}

		$model->reguard();
		$model->sync_original();

		return $model;
	}

	/**
	 * Queries for repos by the provided params.
	 *
	 * @param array $params Query parameters.
	 *
	 * @return Collection<Repo>
	 */
	protected function find_repos_by( array $params = array() ) {
		$collection = new Collection( array(), array(
			'model' => self::REPO_CLASS,
		) );
		$query      = new WP_Query( array_merge( $params, array(
			'post_type'   => 'gistpen',
			'post_parent' => 0,
		) ) );

		foreach ( $query->get_posts() as $post ) {
			$repo = $this->find_repo( $post->ID );

			if ( ! is_wp_error( $repo ) ) {
				$collection->add( $repo );
			}
		}

		return $collection;
	}

	/**
	 * Queries for Blobs by the provided params.
	 *
	 * @param array $params Query parameters.
	 *
	 * @return Collection<Blob>
	 */
	protected function find_blobs_by( array $params = array() ) {
		$collection = new Collection( array(), array(
			'model' => self::BLOB_CLASS,
		) );
		$query      = new WP_Query( array_merge( $params, array(
			'post_type'           => 'gistpen',
			'post_parent__not_in' => array( 0 ),
		) ) );

		foreach ( $query->get_posts() as $post ) {
			$blob = $this->find_blob( $post->ID );

			if ( ! is_wp_error( $blob ) ) {
				$collection->add( $blob );
			}
		}

		return $collection;
	}

	/**
	 * Queries for Languages by the provided params.
	 *
	 * @param array $params Query parameters.
	 *
	 * @return Collection<Language>
	 */
	protected function find_languages_by( $params = array() ) {
		$collection = new Collection( array(), array(
			'model' => self::LANGUAGE_CLASS,
		) );

		$query = new \WP_Term_Query( array_merge( $params, array(
			'taxonomy' => 'wpgp_language',
		) ) );

		foreach ( $query->get_terms() as $term ) {
			$collection->add( new Language( array( Model::OBJECT_KEY => $term ) ) );
		}

		return $collection;
	}

	/**
	 * Persist a new Repo
	 *
	 * @param array $data
	 *
	 * @return Repo|WP_Error
	 */
	protected function create_repo( array $data ) {
		$model = new Repo();

		$blobs = new Collection( array(), array(
			'model' => self::BLOB_CLASS,
		) );

		/**
		 * Set aside the `blobs` key for use.
		 */
		$blobs_data = array();
		if ( isset( $data['blobs'] ) ) {
			if ( is_array( $data['blobs'] ) ) {
				$blobs_data = $data['blobs'];
			}

			unset( $data['blobs'] );
		}

		foreach ( $data as $key => $value ) {
			switch ( $key ) {
				default:
					try {
						$model->set_attribute( $key, $value );
					} catch ( GuardedPropertyException $exception ) {
						// @todo Ignore the value?
					}
					break;
			}
		}

		$result = wp_insert_post( (array) $model->get_underlying_wp_object(), true );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$model->set_attribute( Model::OBJECT_KEY, get_post( $result ) );

		foreach ( $model->get_table_attributes() as $key => $attribute ) {
			update_post_meta(
				$model->get_primary_id(),
				$this->make_meta_key( $key ),
				$attribute
			);
		}

		foreach ( $blobs_data as $blob_data ) {
			$blob_data['post_parent'] = $model->get_primary_id();
			$blob = $this->create_blob( $blob_data );

			if ( ! is_wp_error( $blob ) ) {
				$blobs->add( $blob );
			}
		}

		$model->set_attribute( 'blobs', $blobs );

		return $model;
	}

	/**
	 * Creates a new blob with the provided data.
	 *
	 * @param array $data Blob data.
	 *
	 * @return Blob|WP_Error
	 */
	protected function create_blob( array $data ) {
		$model = new Blob;

		/**
		 * Set aside the `language` key for use.
		 */
		$language_data = array();
		if ( isset( $data['language'] ) ) {
			if ( is_array( $data['language'] ) ) {
				$language_data = $data['language'];
			}

			unset( $data['language'] );
		}

		foreach ( $data as $key => $value ) {
			try {
				$model->set_attribute( $key, $value );
			} catch ( GuardedPropertyException $exception ) {
				// @todo Ignore the value?
			}
		}

		$result = wp_insert_post( (array) $model->get_underlying_wp_object(), true );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$model->set_attribute( Model::OBJECT_KEY, get_post( $result ) );

		foreach ( $model->get_table_attributes() as $key => $attribute ) {
			update_post_meta(
				$model->get_primary_id(),
				$this->make_meta_key( $key ),
				$attribute
			);
		}

		$language = $this->find_languages_by( array( 'slug' => $language_data['slug'] ) );

		if ( count( $language ) === 0 ) {
			$language = $this->create_language( $language_data );

			if ( is_wp_error( $language ) ) {
				return $language;
			}
		} else {
			$language = $language->at( 0 );
		}

		$model->set_attribute( 'language', $language );

		return $model;
	}

	/**
	 * Creates a new Language with the provided data.
	 *
	 * @param array $data Data to create language.
	 *
	 * @return Language|WP_Error
	 */
	protected function create_language( array $data ) {
		$model = new Language;

		foreach ( $data as $key => $value ) {
			try {
				$model->set_attribute( $key, $value );
			} catch ( GuardedPropertyException $exception ) {
				// @todo Ignore the value?
			}
		}

		$result = wp_insert_term( $model->get_underlying_wp_object()->slug, 'wpgp_language' );

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

		return $model;
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

		foreach ( $model->blobs as $blob ) {
			$this->persist_blob( $blob );
		}

		return $model;
	}

	protected function persist_blob( Blob $model ) {
		return new WP_Error( 'not implemented' );
	}

	protected function persist_language( Language $model ) {
		return new WP_Error( 'not implemented' );
	}

	protected function delete_repo( Repo $model, $force ) {
		return new WP_Error( 'not implemented' );
	}

	protected function delete_blob( Blob $model, $force ) {
		return new WP_Error( 'not implemented' );
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
