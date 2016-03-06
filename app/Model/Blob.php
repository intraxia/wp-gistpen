<?php
namespace Intraxia\Gistpen\Model;

use Intraxia\Jaxion\Axolotl\Model;
use Intraxia\Jaxion\Axolotl\Relationship\HasMany;
use Intraxia\Jaxion\Contract\Axolotl\HasEagerRelationships;
use Intraxia\Jaxion\Contract\Axolotl\UsesWordPressPost;

/**
 * Class Blob
 *
 * @package    Intraxia\Gistpen
 * @subpackage Model
 *
 * @property int      $ID
 * @property int      $size
 * @property string   $raw_url
 * @property string   $filename
 * @property string   $code
 * @property Language $language
 * @property Repo     $repo
 */
class Blob extends Model implements UsesWordPressPost, HasEagerRelationships {
	/**
	 * Class name for Repo related class.
	 */
	const REPO_CLASS = 'Intraxia\Gistpen\Model\Repo';

	/**
	 * Class name for Language related class.
	 */
	const LANGUAGE_CLASS = 'Intraxia\Gistpen\Model\Language';

	/**
	 * {@inheritDoc}
	 *
	 * @var array
	 */
	protected $fillable = array(
		'slug',
		'code',
		'language',
	);

	/**
	 * {@inheritDoc}
	 *
	 * @var array
	 */
	protected $guarded = array( 'ID' );

	/**
	 * {@inheritDoc}
	 *
	 * @var array
	 */
	protected $visible = array(
		'ID',
		'size',
		'raw_url',
		'filename',
		'code',
		'language',
	);

	/**
	 * {@inheritDoc}
	 *
	 * @return array
	 */
	public static function get_eager_relationships() {
		return array( 'repo', 'language' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public static function get_post_type() {
		return 'gistpen';
	}

	/**
	 * Maps the Blob's ID to the WP_Post ID.
	 *
	 * @return string
	 */
	protected function map_ID() {
		return 'ID';
	}

	/**
	 * Maps the Blob's slug to the WP_Post post_title.
	 *
	 * @return string
	 */
	protected function map_filename() {
		return 'post_title';
	}

	/**
	 * Maps the Blob's code to the WP_Post post_content.
	 *
	 * @return string
	 */
	protected function map_code() {
		return 'post_content';
	}

	/**
	 * Computes the Blob's size.
	 *
	 * @return int
	 */
	protected function compute_size() {
		return strlen( $this->code );
	}

	/**
	 * Computes the Blob's raw_url.
	 *
	 * @return string
	 */
	protected function compute_raw_url() {
		return rest_url( sprintf(
			'intraxia/v1/gistpen/repos/%s/%s/%s',
			$this->repo->ID,
			$this->ID,
			$this->filename
		) );
	}

	/**
	 * Relates the Blob to its owning Repo.
	 *
	 * @return HasMany
	 */
	public function related_repo() {
		return $this->belongs_to_one(
			self::REPO_CLASS,
			'object',
			'post_parent'
		);
	}

	/**
	 * Retlates the Blob to its Language.
	 *
	 * @return HasMany
	 */
	public function related_language() {
		return $this->belongs_to_one( self::LANGUAGE_CLASS, 'object' );
	}

	/**
	 * Set the language access
	 *
	 * @param string $slug
	 */
	public function set_language_attribute( $slug ) {
		if ( $slug instanceof Language ) {
			$this->language = $slug;
		} elseif ( is_string( $slug ) ) {
			$this->language->slug = $slug;
		}
	}
}
