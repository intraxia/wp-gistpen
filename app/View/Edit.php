<?php
namespace Intraxia\Gistpen\View;

use Intraxia\Gistpen\Contract\Templating;
use Intraxia\Gistpen\Database\EntityManager;
use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Params\Repository as Params;
use Intraxia\Jaxion\Contract\Core\HasActions;
use Intraxia\Jaxion\Contract\Core\HasFilters;
use WP_Query;

/**
 * This class registers all of the settings page views
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Edit implements HasActions, HasFilters {

	/**
	 * Database service.
	 *
	 * @var EntityManager
	 */
	protected $em;

	/**
	 * User options service.
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
	 * Plugin path string.
	 *
	 * @var string
	 * @since 0.6.0
	 */
	protected $path;

	/**
	 * Plugin url string.
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
	 * @param Params        $params
	 * @param Templating    $templating
	 * @param string        $path
	 * @param string        $url
	 */
	public function __construct( EntityManager $em, Params $params, Templating $templating, $path, $url ) {
		$this->em         = $em;
		$this->params     = $params;
		$this->templating = $templating;
		$this->path       = $path;
		$this->url        = $url;
	}

	/**
	 * Echoes the editor on the gistpen editor page.
	 */
	public function display_editor() {
		$post = get_post();

		if ( 'gistpen' === $post->post_type && 0 === $post->post_parent ) {
			echo '<div id="edit-app"></div>';
		}
	}

	/**
	 * Set layout of the post table view to one column.
	 *
	 * @param array $columns The number of columns in a given list page.
	 * @return array
	 * @since  0.4.0
	 */
	public function screen_layout_columns( $columns ) {
		$columns['gistpen'] = 1;
		return $columns;
	}

	/**
	 * Set the number of columns on the edit view.
	 *
	 * @return int
	 */
	public function screen_layout_gistpen() {
		return 1;
	}

	/**
	 * Remove unnecessary metaboxes from Gistpens
	 *
	 * @since  0.4.0
	 */
	public function remove_meta_boxes() {
		remove_meta_box( 'submitdiv', 'gistpen', 'side' );
		remove_meta_box( 'commentsdiv', 'gistpen', 'normal' );
		remove_meta_box( 'revisionsdiv', 'gistpen', 'normal' );
		remove_meta_box( 'authordiv', 'gistpen', 'normal' );
		remove_meta_box( 'slugdiv', 'gistpen', 'normal' );
		remove_meta_box( 'tagsdiv-post_tag', 'gistpen', 'side' );
		remove_meta_box( 'categorydiv', 'gistpen', 'side' );
		remove_meta_box( 'gistpenexcerpt', 'gistpen', 'normal' );
		remove_meta_box( 'formatdiv', 'gistpen', 'normal' );
		remove_meta_box( 'trackbacksdiv', 'gistpen', 'normal' );
		remove_meta_box( 'gistpencustom', 'gistpen', 'normal' );
		remove_meta_box( 'commentstatusdiv', 'gistpen', 'normal' );
		remove_meta_box( 'postimagediv', 'gistpen', 'side' );
	}

	/**
	 * Adds the file column to the Gistpen edit screen
	 *
	 * @param  array $columns Array of the columns.
	 * @return array          Array with new column added
	 * @since  0.4.0
	 */
	public function manage_posts_columns( $columns ) {
		return array_merge( $columns, array(
			'wpgp_blobs' => __( 'Blobs', 'wp-gistpen' ),
		) );
	}

	/**
	 * Render the file column on the Gistpen edit screen
	 *
	 * @param  string $column_name the custom column name.
	 * @param  int    $post_id     the ID of the current post.
	 * @since  0.4.0
	 */
	public function manage_posts_custom_column( $column_name, $post_id ) {
		if ( 'wpgp_blobs' === $column_name ) {
			/**
			 * The repo in the column.
			 *
			 * @var Repo
			 */
			$repo = $this->em->find( EntityManager::REPO_CLASS, $post_id, array(
				'with' => 'blobs',
			) );

			/**
			 * Individual blob.
			 *
			 * @var Blob
			 */
			foreach ( $repo->blobs as $blob ) {
				echo esc_html( $blob->filename );
				echo '<br>';
			}
		}
	}

	/**
	 * Reorders in reverse chron on the Gistpen edit screen
	 *
	 * @param  string   $orderby the query's orderby statement.
	 * @param  WP_Query $query   current query obj.
	 * @return string          new orderby statement
	 * @since  0.4.0
	 */
	public function edit_screen_orderby( $orderby, $query ) {
		if ( is_admin() && 'gistpen' === $query->query_vars['post_type'] ) {
			global $wpdb;
			$orderby = $wpdb->posts . '.post_date DESC';
		}

		return $orderby;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array[]
	 */
	public function action_hooks() {
		return array(
			array(
				'hook'   => 'edit_form_top',
				'method' => 'display_editor',
			),
			array(
				'hook'   => 'add_meta_boxes',
				'method' => 'remove_meta_boxes',
			),
			array(
				'hook'   => 'manage_gistpen_posts_custom_column',
				'method' => 'manage_posts_custom_column',
				'args'   => 2,
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
				'hook'   => 'screen_layout_columns',
				'method' => 'screen_layout_columns',
			),
			array(
				'hook'   => 'get_user_option_screen_layout_gistpen',
				'method' => 'screen_layout_gistpen',
			),
			array(
				'hook'   => 'manage_gistpen_posts_columns',
				'method' => 'manage_posts_columns',
			),
			array(
				'hook'   => 'posts_orderby',
				'method' => 'edit_screen_orderby',
				'args'   => 2,
			),
		);
	}
}
