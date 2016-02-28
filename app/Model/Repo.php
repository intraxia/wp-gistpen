<?php
namespace Intraxia\Gistpen\Model;

use Intraxia\Jaxion\Axolotl\Model;
use Intraxia\Jaxion\Contract\Axolotl\UsesWordPressPost;

/**
 * Class Repo
 *
 * @package Intraxia\Gistpen
 * @subpackage Model
 */
class Repo extends Model implements UsesWordPressPost {
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
//		'blobs',
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
		return rest_url( 'intraxia/v1/gistpen/repos/' . $this->get_attribute( 'ID' ) );
	}

	/**
	 * Computes the Repo's commits_url.
	 *
	 * @return string
	 */
	protected function compute_commits_url() {
		return rest_url( 'intraxia/v1/gistpen/repos/' . $this->get_attribute( 'ID' ) . '/commits' );
	}

	/**
	 * Computes the Repo's html_url.
	 *
	 * @return string
	 */
	public function compute_html_url() {
		return get_permalink( $this->get_attribute( 'ID' ) );
	}
}
