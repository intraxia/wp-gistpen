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
	protected static $defaults = array(
		'id'        => 0,
		'highlight' => '',
		'offset'    => 0,
	);

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
	 * Post currently being processed by shortcode.
	 *
	 * @var WP_Post
	 */
	private $shortcode_post;

	/**
	 * Highlighting for the current shortcode.
	 *
	 * @var string
	 */
	private $shortcode_highlight;

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
	 * @param Params     $params
	 * @param Templating $templating
	 * @param Assets     $assets
	 */
	public function __construct( Params $params, Templating $templating, Assets $assets ) {
		$this->params     = $params;
		$this->templating = $templating;
		$this->assets     = $assets;
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
		$post = get_post(); // @codingStandardsIgnoreLine

		if ( Repo::get_post_type() !== $post->post_type ) {
			return $content;
		}

		if ( ! $post->post_parent ) {
			return $this->templating->render(
				'repo',
				$this->params->props( 'content.repo' )
			);
		}

		return $this->templating->render(
			'blob',
			$this->params->props( 'content.blob' )
		);
	}

	/**
	 * Filter the child posts from the main query
	 *
	 * @param  \WP_Query $query Query object.
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
			return '<div class="wp-gistpen-error">' . __( 'No Gistpen ID was provided.', 'wp-gistpen' ) . '</div>';
		}

		$args['id']        = (int) str_replace( '&quot;', '', $args['id'] );
		$args['highlight'] = str_replace( '&quot;', '', $args['highlight'] );

		if ( Repo::get_post_type() !== get_post_type( $args['id'] ) ) {
			return '<div class="wp-gistpen-error">' . __( 'ID provided is not a Gistpen repo.', 'wp-gistpen' ) . '</div>';
		}

		global $post;
		$post_bu = $post;
		$post = get_post( $args['id'] ); // @codingStandardsIgnoreLine

		if ( ! $post->post_parent ) {
			$content = $this->templating->render(
				'repo',
				$this->params->props( 'content.repo' )
			);
		} else {
			$content = $this->templating->render(
				'blob',
				$this->params->props( 'content.blob', array(
					'highlight' => $args['highlight'],
					'offset'    => $args['offset'],
				) )
			);
		}

		$post = $post_bu; // @codingStandardsIgnoreLine

		return $content;
	}

	/**
	 * Enqueues the js required to highlight the embed.
	 */
	public function enqueue_embed_scripts() {
		$post = get_post(); // @codingStandardsIgnoreLine

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
		$post = get_post( $id ); // @codingStandardsIgnoreLine

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
	 * @param string  $output Embed html output.
	 * @param WP_Post $post   Associated post.
	 *
	 * @return string New embed html output.
	 */
	public function remove_embed_width( $output, WP_Post $post ) {
		if ( Repo::get_post_type() === $post->post_type ) {
			return preg_replace( '/width="\d+"/', 'width="100%"', $output );
		}

		return $output;
	}

	/**
	 * Replaces the styles injected by WordPress with our own for the embed.
	 */
	public function inject_styles() {
		if ( get_post_type() === Repo::get_post_type() ) {
			remove_action( 'embed_head', 'print_embed_styles' );
			remove_action( 'embed_content_meta', 'print_embed_comments_button' );
			remove_action( 'embed_content_meta', 'print_embed_sharing_button' );
			remove_action( 'embed_footer', 'print_embed_sharing_dialog' );
			remove_action( 'embed_footer', 'print_embed_scripts' );

			// @codingStandardsIgnoreLine
			echo <<<CSS
<style>
body {
    margin: 0;
}

.wp-embed-footer,
.wp-embed-heading {
    display: none;
}

.wp-embed-excerpt pre[class*="language-"] {
    margin: 0;
}
</style>
CSS;

		}
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array[]
	 */
	public function action_hooks() {
		return array(
			array(
				'hook'     => 'embed_head',
				'method'   => 'inject_styles',
				'priority' => 5,
			),
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
			),
		);
	}
}
