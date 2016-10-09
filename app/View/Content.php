<?php
namespace Intraxia\Gistpen\View;

/**
 * Registers the front-end content output
 *
 * @package    Intraxia\Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */

use Intraxia\Gistpen\Contract\Templating;
use Intraxia\Gistpen\Database\EntityManager as EM;
use Intraxia\Gistpen\Facade\Database;
use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Model\Zip;
use Intraxia\Gistpen\Options\Site;
use Intraxia\Jaxion\Contract\Axolotl\EntityManager;
use Intraxia\Jaxion\Contract\Core\HasActions;
use Intraxia\Jaxion\Contract\Core\HasFilters;
use Intraxia\Jaxion\Contract\Core\HasShortcode;

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
	 * Database Facade object
	 *
	 * @var EntityManager
	 * @since 0.5.0
	 */
	protected $em;

	/**
	 * Site options.
	 *
	 * @var Site
	 */
	protected $site;

	/**
	 * Templating service.
	 *
	 * @var Templating
	 */
	protected $templating;

	/**
	 * Plugin url.
	 *
	 * @var string
	 */
	protected $url;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 *
	 * @param EntityManager $em
	 * @param Site          $site
	 * @param Templating    $templating
	 * @param string        $url
	 */
	public function __construct( EntityManager $em, Site $site, Templating $templating, $url ) {
		$this->em = $em;
		$this->site = $site;
		$this->templating = $templating;
		$this->url = $url;
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
		if ( 'gistpen' === get_post_type() ) {
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

		if ( 'gistpen' !== $post->post_type ) {
			return $content;
		}

		if ( ! $post->post_parent ) {
			/** @var Repo $repo */
			$repo = $this->em->find( EM::REPO_CLASS, $post->ID );

			if ( is_wp_error( $repo ) ) {
				return $content;
			}

			return $this->templating->render(
				'component/repo/index',
				$this->merge_state( array( 'repo' => $repo->serialize() ) )
			);
		}

		/** @var Blob $repo */
		$blob = $this->em->find( EM::BLOB_CLASS, $post->ID );

		if ( is_wp_error( $blob  ) ) {
			return $content;
		}

		return $this->templating->render(
			'component/blob/index',
			$this->merge_state( array( 'blob' => $blob->serialize() ) )
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

		if ( ! $query->is_post_type_archive( 'gistpen' ) ) {
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

		if ( 'gistpen' !== $post->post_type ) {
			return '<div class="wp-gistpen-error">ID provided is not a Gistpen repo.</div>';
		}

		if ( $post->post_parent === 0 ) {
			/** @var Repo|\WP_Error $model */
			$model = $this->em->find( EM::REPO_CLASS, $post->ID );
		} else {
			/** @var Blob|\WP_Error $model */
			$model = $this->em->find( EM::BLOB_CLASS, $post->ID );
		}

		if ( is_wp_error( $model ) ) {
			return '<div class="wp-gistpen-error">Error: ' . $model->get_error_message() .'.</div>';
		}

		return get_post_embed_html( 'auto', 'auto', $args['id'] );
	}

	/**
	 * Gets the default initial state for the Content.
	 *
	 * @return array
	 */
	public function get_initial_state() {
		return array(
			'prism' => $this->site->get( 'prism' ),
			'url'   => $this->url,
		);
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
		);
	}

	/**
	 * Gets the initial state for the Content output.
	 *
	 * @param array $state
	 *
	 * @return array
	 */
	protected function merge_state( array $state ) {
		return array_merge(
			$this->get_initial_state(),
			$state
		);
	}
}

