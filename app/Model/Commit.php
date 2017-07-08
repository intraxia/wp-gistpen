<?php
namespace Intraxia\Gistpen\Model;

use Intraxia\Jaxion\Axolotl\Collection;
use Intraxia\Jaxion\Axolotl\Model;
use Intraxia\Jaxion\Contract\Axolotl\UsesWordPressPost;

/**
 * Class Commit
 *
 * @package    Intraxia\Gistpen
 * @subpackage Model
 *
 * @property int        $ID
 * @property int        $repo_id
 * @property string     $description
 * @property Collection $blobs
 * @property string     $rest_url
 * @property string     $commits_url
 * @property string     $html_url
 * @property string     $committed_at
 */
class Commit extends Model implements UsesWordPressPost {

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	protected $defaults = array(
		'description'  => '',
		'committed_at' => '',
	);

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	protected $fillable = array(
		'description',
	);

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	protected $guarded = array(
		'blobs',
		'committed_at',
	);

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	protected $visible = array(
		'ID',
		'repo_id',
		'description',
		'blobs',
		'rest_url',
		'commits_url',
		'html_url',
		'committed_at',
	);

	/**
	 * {@inheritdoc}
	 *
	 * @return string
	 */
	public static function get_post_type() {
		return 'revision';
	}

	/**
	 * Maps the Commit's ID to WP_Posts's ID.
	 *
	 * @return string
	 */
	protected function map_ID() {
		return 'ID';
	}

	/**
	 * Maps the Commit's description to WP_Posts's post_title.
	 *
	 * @return string
	 */
	protected function map_description() {
		return 'post_title';
	}

	/**
	 * Maps the Commit's repo_i to the WP_Post's post_parent.
	 *
	 * @return string
	 */
	protected function map_repo_id() {
		return 'post_parent';
	}

	/**
	 * Maps the Commit's created_at time to WP_Posts's post_status.
	 *
	 * @return string
	 */
	protected function map_committed_at() {
		return 'post_date';
	}
}
