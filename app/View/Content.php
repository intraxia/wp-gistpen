<?php
namespace Intraxia\Gistpen\View;

use Intraxia\Gistpen\Params\Repository as Params;
use Intraxia\Jaxion\Assets\Register as Assets;

/**
 * Registers the front-end content output
 *
 * @package    Intraxia\Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */

use Intraxia\Gistpen\Contract\Templating;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Jaxion\Contract\Core\HasActions;
use Intraxia\Jaxion\Contract\Core\HasFilters;
use Intraxia\Jaxion\Contract\Core\HasShortcode;
use WP_Post;

/**
 * This class manipulates the Gistpen post content.
 *
 * @package Content
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class Content implements HasActions, HasFilters, HasShortcode {
	/**
	 * Shortcode defaults.
	 *
	 * @var array
	 */
	protected static $defaults = array( 'id' => null );

	/**
	 * Params service.
	 *
	 * @var Params
	 */
	protected $params;

	/**
	 * Templating service.
	 *
	 * @var Templating
	 */
	protected $templating;

	/**
	 * Assets service provider.
	 *
	 * @var Assets
	 */
	private $assets;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 *
	 * @param Params $params
	 * @param Templating    $templating
	 * @param Assets        $assets
	 */
	public function __construct( Params $params, Templating $templating, Assets $assets) {
		$this->params = $params;
		$this->templating = $templating;
		$this->assets = $assets;
	}

	/**
	 * Remove extra filters from the Gistpen content
	 *
	 * @param string $content
	 *
	 * @return string
	 * @since    0.1.0
	 */
	public function remove_filters( $content ) {
		if ( Repo::get_post_type() === get_post_type() ) {
			remove_filter( 'the_content', 'wpautop' );
			remove_filter( 'the_content', 'wptexturize' );
			remove_filter( 'the_content', 'capital_P_dangit' );
			remove_filter( 'the_content', 'convert_chars' );
			remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );
		}

		return $content;
	}

	/**
	 * Add the Gistpen content field to the_content
	 *
	 * @param string $content
	 *
	 * @return string post_content
	 * @since    0.1.0
	 */
	public function post_content( $content = '' ) {
		$post = get_post();

		if ( Repo::get_post_type() !== $post->post_type ) {
			return $content;
		}

		if ( ! $post->post_parent ) {
			return $this->templating->render(
				'component/repo/index',
				$this->params->props( 'content.repo' )
			);
		}

		return $this->templating->render(
			'component/blob/index',
			$this->params->props( 'content.blob' )
		);
	}

	/**
	 * Filter the child posts from the main query
	 *
	 * @param  \WP_Query $query query object
	 *
	 * @return \WP_Query
	 * @since  0.4.0
	 */
	public function pre_get_posts( $query ) {
		if ( ! $query->is_main_query() ) {
			return $query;
		}

		if ( ! $query->is_post_type_archive( Repo::get_post_type() ) ) {
			return $query;
		}

		// only top level posts
		$query->set( 'post_parent', 0 );

		return $query;
	}


	/**
	 * {@inheritDoc}
	 *
	 * @return string
	 */
	public function shortcode_name() {
		return 'gistpen';
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param array  $attrs attributes passed into the shortcode.
	 * @param string $content
	 *
	 * @return   string
	 * @since    0.1.0
	 */
	public function do_shortcode( array $attrs, $content = '' ) {
		$args = shortcode_atts( static::$defaults, $attrs, 'gistpen' );

		// If the user didn't provide an ID, raise an error
		if ( ! $args['id'] ) {
			return '<div class="wp-gistpen-error">No Gistpen ID was provided.</div>';
		}

		$post = get_post( $args['id'] );

		if ( Repo::get_post_type() !== $post->post_type ) {
			return '<div class="wp-gistpen-error">ID provided is not a Gistpen repo.</div>';
		}

		return wp_oembed_get( get_post_embed_url( $post ) );
	}

	/**
	 * Enqueues the js required to highlight the embed.
	 */
	public function enqueue_embed_scripts() {
		$post = get_post();

		if ( Repo::get_post_type() !== $post->post_type ) {
			return;
		}

		$this->assets->enqueue_web_scripts();
	}

	/**
	 * Remove the title from the Gistpen oembed.
	 *
	 * @param string $title Post title.
	 * @param int    $id    Post ID.
	 *
	 * @return string
	 */
	public function remove_embed_title( $title, $id ) {
		$post = get_post( $id );

		if (
			is_embed() &&
			Repo::get_post_type() === $post->post_type &&
			0 !== $post->post_parent // only remove the title from `Blob` embeds
		) {
			return '';
		}

		return $title;
	}

	/**
	 * Remove the hard-coded width so the embed can scale with the width of the column.
	 *
	 * @param string $output Embed html output.
	 * @param WP_Post $post  Associated post.
	 *
	 * @return string New embed html output.
	 */
	public function remove_embed_width( $output, WP_Post $post ) {
		if ( Repo::get_post_type() === $post->post_type ) {
			return preg_replace('/width="\d+"/', 'width="100%"', $output );
		}

		return $output;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array[]
	 */
	public function action_hooks() {
		return array(
			array(
				'hook'   => 'the_content',
				'method' => 'remove_filters',
			),
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array[]
	 */
	public function filter_hooks() {
		return array(
			array(
				'hook'     => 'the_content',
				'method'   => 'post_content',
				'priority' => 20,
			),
			array(
				'hook'   => 'pre_get_posts',
				'method' => 'pre_get_posts',
			),
			array(
				'hook'   => 'the_excerpt_embed',
				'method' => 'post_content',
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
			array(
				'hook'   => 'embed_html',
				'method' => 'remove_embed_width',
				'args'   => 2,
			)
		);
	}
}

