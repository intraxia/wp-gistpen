<?php
namespace Intraxia\Gistpen\Model;

use Intraxia\Jaxion\Axolotl\Model;
use Intraxia\Jaxion\Contract\Axolotl\UsesWordPressPost;

/**
 * Class Blob
 *
 * @package    Intraxia\Gistpen
 * @subpackage Model
 *
 * @property int      $ID
 * @property string   $filename
 * @property string   $code
 * @property int      $repo_id
 * @property Repo     $repo
 * @property Language $language
 * @property int      $size
 * @property string   $raw_url
 * @property string   $edit_url
 */
class Blob extends Model implements UsesWordPressPost {
	/**
	 * {@inheritDoc}
	 *
	 * @var array
	 */
	protected $fillable = array(
		'code',
		'filename',
		'language',
	);

	/**
	 * {@inheritDoc}
	 *
	 * @var array
	 */
	protected $guarded = array(
		'ID',
		'repo_id',
		'repo',
		'status',
	);

	/**
	 * {@inheritDoc}
	 *
	 * @var array
	 */
	protected $visible = array(
		'ID',
		'size',
		'raw_url',
		'edit_url',
		'filename',
		'code',
		'language',
	);

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
	 * Maps the Repo's status to WP_Posts's post_status.
	 *
	 * @return string
	 */
	protected function map_status() {
		return 'post_status';
	}

	/**
	 * Maps the Blob's repo_id to the WP_Post post_parent.
	 *
	 * @return string
	 */
	protected function map_repo_id() {
		return 'post_parent';
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
			'intraxia/v1/gistpen/repos/%s/blobs/%s/raw',
			$this->repo_id,
			$this->ID
		) );
	}

	/**
	 * Computes the edit url of the Blob.
	 *
	 * @return string
	 */
	protected function compute_edit_url() {
		return get_edit_post_link( $this->repo_id );
	}
}
