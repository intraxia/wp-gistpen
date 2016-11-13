<?php
namespace Intraxia\Gistpen\Model;

use Intraxia\Gistpen\Database\EntityManager;
use Intraxia\Jaxion\Axolotl\Collection;
use Intraxia\Jaxion\Axolotl\Model;
use Intraxia\Jaxion\Contract\Axolotl\UsesWordPressPost;

/**
 * Class Repo
 *
 * @package    Intraxia\Gistpen
 * @subpackage Model
 *
 * @property int        $ID
 * @property string     $description
 * @property string     $status
 * @property string     $password
 * @property string     $gist_id
 * @property string     $sync
 * @property Collection $blobs
 * @property string     $rest_url
 * @property string     $commits_url
 * @property string     $html_url
 * @property string     $created_at
 * @property string     $updated_at
 */
class Repo extends Model implements UsesWordPressPost {
	/**
	 * Class name for Blob related class.
	 */
	const BLOB_CLASS = 'Intraxia\Gistpen\Model\Blob';

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	protected $fillable = array(
		'description',
		'status',
		'password',
		'sync',
		'blobs',
	);

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	protected $guarded = array(
		'gist_id',
		'created_at',
		'updated_at',
	);

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	protected $visible = array(
		'ID',
		'description',
		'status',
		'password',
		'gist_id',
		'sync',
		'blobs',
		'rest_url',
		'commits_url',
		'html_url',
		'created_at',
		'updated_at',
	);

	/**
	 * {@inheritdoc}
	 *
	 * @return string
	 */
	public static function get_post_type() {
		return 'gistpen';
	}

	/**
	 * Maps the Repo's ID to WP_Posts's ID.
	 *
	 * @return string
	 */
	protected function map_ID() {
		return 'ID';
	}

	/**
	 * Maps the Repo's description to WP_Posts's post_title.
	 *
	 * @return string
	 */
	protected function map_description() {
		return 'post_title';
	}

	/**
	 * Maps the Repo's password to WP_Posts's post_password.
	 *
	 * @return string
	 */
	protected function map_password() {
		return 'post_password';
	}

	/**
	 * Maps the Repo's status to WP_Posts's post_status.
	 *
	 * @return string
	 */
	protected function map_status() {
		return 'post_status';
	}

	/**
	 * Maps the Repo's created_at time to WP_Posts's post_status.
	 *
	 * @return string
	 */
	protected function map_created_at() {
		return 'post_date';
	}

	/**
	 * Maps the Repo's updated_at time to WP_Posts's post_modified.
	 *
	 * @return string
	 */
	protected function map_updated_at() {
		return 'post_modified';
	}

	/**
	 * Computes the Repo's rest_url.
	 *
	 * @return string
	 */
	protected function compute_rest_url() {
		return rest_url( sprintf(
			'intraxia/v1/gistpen/repos/%s',
			$this->ID
		) );
	}

	/**
	 * Computes the Repo's commits_url.
	 *
	 * @return string
	 */
	protected function compute_commits_url() {
		return rest_url( sprintf(
			'intraxia/v1/gistpen/repos/%s/commits',
			$this->ID
		) );
	}

	/**
	 * Computes the Repo's html_url.
	 *
	 * @return string
	 */
	protected function compute_html_url() {
		return get_permalink( $this->ID );
	}

	/**
	 * {@inheritDoc}
	 */
	public function refresh( array $attributes ) {
		try {
			$blobs = $this->blobs;
		} catch ( \Exception $exception ) {
			$blobs = new Collection( array(), array(
				'model' => EntityManager::BLOB_CLASS,
			) );
		}

		if ( isset( $attributes['blobs'] ) ) {
			if ( $attributes['blobs'] instanceof Collection ) {
				$blobs = $attributes['blobs'];
			} else {
				foreach ( $attributes['blobs'] as $attribute ) {
					$matched = false;

					if ( isset( $attribute['language'] ) && is_string( $attribute['language'] ) ) {
						$attribute['language'] = new Language( array( 'slug' => $attribute['language'] ) );
					}

					/** @var Blob $blob */
					foreach ( $blobs as $blob ) {
						if ( (string) $blob->ID === (string) $attribute['ID'] ) {
							unset( $attribute['ID'] );
							$blob->refresh( $attribute );
							$matched = true;
							break;
						}
					}

					if ( ! $matched ) {
						$blobs->add( new Blob( $attribute ) );
					}
				}
			}


			unset( $attributes['blobs'] );
		}

		parent::refresh( $attributes );

		$this->blobs = $blobs;
	}

	/**
	 * {@inheritDoc}
	 */
	public function merge( array $attributes ) {
		try {
			$blobs = $this->blobs;
		} catch ( \Exception $exception ) {
			$blobs = new Collection( array(), array(
				'model' => EntityManager::BLOB_CLASS,
			) );
		}

		if ( isset( $attributes['blobs'] ) ) {
			if ( $attributes['blobs'] instanceof Collection ) {
				$blobs = $attributes['blobs'];
			} else {
				foreach ( $attributes['blobs'] as $attribute ) {
					$matched = false;

					/** @var Blob $blob */
					foreach ( $blobs as $blob ) {
						if ( $blob->ID === $attribute['ID'] ) {
							unset( $attribute['ID'] );
							if ( isset( $attribute['language'] ) && is_string( $attribute['language'] ) ) {
								$attribute['language'] = new Language( array( 'slug' => $attribute['language'] ) );
							}
							$blob->merge( $attribute );
							$matched = true;
							break;
						}
					}

					if ( ! $matched ) {
						$blobs->add( new Blob( $attribute ) );
					}
				}
			}

			unset( $attributes['blobs'] );
		}

		parent::merge( $attributes );

		$this->blobs = $blobs;
	}
}
