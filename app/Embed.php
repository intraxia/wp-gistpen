<?php
namespace Intraxia\Gistpen;

use Intraxia\Gistpen\Database\Query\Head;
use Intraxia\Gistpen\Facade\Database;
use Intraxia\Gistpen\Model\File;
use Intraxia\Gistpen\Model\Zip;
use Intraxia\Jaxion\Assets\Register as Assets;
use Intraxia\Jaxion\Contract\Core\HasFilters;
use Closure;

class Embed implements HasFilters {
	/**
	 * Plugin path
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Localization closure
	 *
	 * @var Assets
	 */
	protected $assets;

	/**
	 * Plugin url
	 *
	 * @var string
	 */
	protected $url;

	/**
	 * Database facade
	 *
	 * @var Database
	 */
	private $database;

	/**
	 * Embed constructor.
	 *
	 * @param Database $database
	 * @param Assets   $assets
	 * @param string   $path
	 * @param string   $url
	 */
	public function __construct( Database $database, Assets $assets, $path, $url ) {
		$this->database = $database;
		$this->assets = $assets;
		$this->path = $path;
		$this->url = $url;
	}

	/**
	 * Retrieves the content to be used in an oEmbeded Gistpen.
	 *
	 * Replaces the standard excerpt
	 *
	 * @param $output
	 *
	 * @return string
	 */
	public function get_embed_excerpt( $output ) {
		$post = get_post();

		if ( 'gistpen' !== $post->post_type ) {
			return $output;
		}

		/** @var Head $query */
		$query = $this->database->query( 'head' );
		$model = $query->by_post( $post );

		if ( is_wp_error( $model ) ) {
			return $output;
		}

		// @todo implement against contract
		if ( $model instanceof Zip || $model instanceof File ) {
			return $model->get_shortcode_content();
		}

		return $output;
	}

	/**
	 * Enqueues the js required to highlight the embed.
	 */
	public function enqueue_embed_scripts() {
		$post = get_post();

		if ( 'gistpen' !== $post->post_type ) {
			return;
		}

		$this->assets->enqueue_web_scripts();
	}

	/**
	 * Remove the title from the Gistpen oembed.
	 *
	 * @param string $title Post title.
	 * @param int    $id Post ID.
	 *
	 * @return string
	 */
	public function remove_embed_title( $title, $id ) {
		$post = get_post( $id );

		if (
			is_embed() &&
			'gistpen' === $post->post_type &&
			0 !== $post->post_parent // only remove the title from `File` embeds
		) {
			return '';
		}

		return $title;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array[]
	 */
	public function filter_hooks() {
		return array(
			array(
				'hook'   => 'the_excerpt_embed',
				'method' => 'get_embed_excerpt',
			),
			array(
				'hook'   => 'embed_footer',
				'method' => 'enqueue_embed_scripts',
			),
			array(
				'hook'   => 'the_title',
				'method' => 'remove_embed_title',
				'args'   => 2,
			),
		);
	}
}
