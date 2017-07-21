<?php
namespace Intraxia\Gistpen\Model;

use Intraxia\Jaxion\Axolotl\Model;
use Intraxia\Jaxion\Contract\Axolotl\UsesWordPressPost;

/**
 * Class State
 *
 * @package    Intraxia\Gistpen
 * @subpackage Model
 *
 * @property int      $ID
 * @property int      $blob_id
 * @property Blob     $blob
 * @property string   $filename
 * @property string   $code
 * @property Language $language
 * @property int      $size
 * @property string   $raw_url
 */
class State extends Model implements UsesWordPressPost {
	/**
	 * {@inheritDoc}
	 *
	 * @var array
	 */
	protected $fillable = array(
		'code',
		'filename',
		'language',
		'blob_id',
		'blob',
	);

	/**
	 * {@inheritDoc}
	 *
	 * @var array
	 */
	protected $guarded = array(
		'ID',
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
		return 'revision';
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
	 * Maps the Blob's blob_id to the WP_Post post_parent.
	 *
	 * @return string
	 */
	protected function map_blob_id() {
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
}
