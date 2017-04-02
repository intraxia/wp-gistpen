<?php
namespace Intraxia\Gistpen\View;

use Intraxia\Gistpen\Contract\Templating;
use Intraxia\Gistpen\Database\EntityManager;
use Intraxia\Gistpen\Facade\Adapter;
use Intraxia\Gistpen\Facade\Database;
use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Model\Language;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Options\User;
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
class Editor implements HasActions, HasFilters {
	/**
	 * All the Ace themes for select box
	 *
	 * @var array
	 * @since    0.4.0
	 */
	public static $ace_themes = array(
		'ambiance' => 'Ambiance',
		'chaos' => 'Chaos',
		'chrome' => 'Chrome',
		'clouds' => 'Clouds',
		'clouds_midnight' => 'Clouds Midnight',
		'cobalt' => 'Cobalt',
		'crimson_editor' => 'Crimson Editor',
		'dawn' => 'Dawn',
		'dreamweaver' => 'Dreamweaver',
		'eclipse' => 'Eclipse',
		'github' => 'GitHub',
		'idle_fingers' => 'Idle Fingers',
		'katzenmilch' => 'Katzenmilch',
		'kr_theme' => 'KR',
		'kuroir' => 'Kuroir',
		'merbivore' => 'Merbivore',
		'monokai' => 'Monokai',
		'solarized_dark' => 'Solarized Dark',
		'solarized_light' => 'Solarized Light',
		'twilight' => 'Twilight',
	);

	/**
	 * Database Facade object
	 *
	 * @var EntityManager
	 * @since 0.5.0
	 */
	protected $em;

	/**
	 * User options service.
	 *
	 * @var User
	 */
	protected $user;

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
	 * @param User          $user
	 * @param Templating    $templating
	 * @param string        $path
	 * @param string        $url
	 */
	public function __construct( EntityManager $em, User $user, Templating $templating, $path, $url ) {
		$this->em         = $em;
		$this->user       = $user;
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
			echo $this->templating->render( 'page/editor/index', $this->get_initial_state() );
		}
	}

	/**
	 * Returns the initial state for the editor page.
	 *
	 * @return array
	 */
	public function get_initial_state() {
		/** @var Repo $repo */
		$repo = $this->em->find( EntityManager::REPO_CLASS, get_the_ID() );

		// @todo move to accessors?
		if ( 'auto-draft' === $repo->status ) {
			$repo->status = 'draft';
			$repo->description = '';
			$repo->sync = 'off';

			$repo->blobs->add( new Blob( array(
				'filename' => '',
				'code' => '',
				'language' => $this->em->find_by( EntityManager::LANGUAGE_CLASS, array( 'slug' => 'plaintext' ) )->at( 0 ),
			) ) );
		}

		$blobs = iterator_to_array( $repo->blobs );
		usort( $blobs, function( $a, $b ) {
			return (int) $a->ID - (int) $b->ID;
		} );

		return array(
			'repo'   => $repo->serialize(),
			'editor' => array(
				'description' => $repo->description,
				'status' => $repo->status,
				'password' => $repo->password,
				'gist_id' => $repo->gist_id,
				'sync' => $repo->sync,
				'instances'  => array_map( function ( Blob $blob ) {
					return array(
						'key'      => (string) $blob->ID ? : 'new0',
						'filename' => $blob->filename,
						'code'     => $blob->code,
						'language' => $blob->language->slug,
						'cursor'   => false,
						'history'  => array(
							'undo' => array(),
							'redo' => array(),
						),
					);
				}, $blobs ),
				'width'      => $this->user->get( 'editor.indent_width' ),
				'theme'      => $this->user->get( 'editor.theme' ),
				'invisibles' => $this->user->get( 'editor.invisibles_enabled' ) ? : 'off',
				'tabs'       => $this->user->get( 'editor.tabs_enabled' ) ? : 'off',
				'widths'     => array( '1', '2', '4', '8' ),
				'themes'     => array(
					'default'                         => __( 'Default', 'wp-gistpen' ),
					'dark'                            => __( 'Dark', 'wp-gistpen' ),
					'funky'                           => __( 'Funky', 'wp-gistpen' ),
					'okaidia'                         => __( 'Okaidia', 'wp-gistpen' ),
					'tomorrow'                        => __( 'Tomorrow', 'wp-gistpen' ),
					'twilight'                        => __( 'Twilight', 'wp-gistpen' ),
					'coy'                             => __( 'Coy', 'wp-gistpen' ),
					'cb'                              => __( 'CB', 'wp-gistpen' ),
					'ghcolors'                        => __( 'GHColors', 'wp-gistpen' ),
					'pojoaque'                        => __( 'Projoaque', 'wp-gistpen' ),
					'xonokai'                         => __( 'Xonokai', 'wp-gistpen' ),
					'base16-ateliersulphurpool-light' => __( 'Ateliersulphurpool-Light', 'wp-gistpen' ),
					'hopscotch'                       => __( 'Hopscotch', 'wp-gistpen' ),
					'atom-dark'                       => __( 'Atom Dark', 'wp-gistpen' ),
				),
				'statuses'   => get_post_statuses(),
				'languages'  => Language::$supported,
				'optionsOpen' => true,
			),
			'api'    => array(
				'root'  => esc_url_raw( rest_url() . 'intraxia/v1/gistpen/' ),
				'nonce' => wp_create_nonce( 'wp_rest' ),
				'url'   => $this->url,
			),
		);
	}

	/**
	 * Force the Gistpen layout to one column
	 *
	 * @since  0.4.0
	 */
	public function screen_layout_columns( $columns ) {
		$columns['gistpen'] = 1;
		return $columns;
	}
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
			'gistpen_files' => __( 'Files', 'wp-gistpen' ),
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
		if ( 'gistpen_files' === $column_name ) {
			/** @var Repo $repo */
			$repo = $this->em->find( EntityManager::REPO_CLASS,  $post_id );

			/** @var Blob $blob */
			foreach ( $repo->blobs as $blob ) {
				echo $blob->filename;
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
		if ( is_admin() && $query->query_vars['post_type'] === 'gistpen' ) {
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
				'hook' => 'edit_form_top',
				'method' => 'display_editor',
			),
			array(
				'hook' => 'add_meta_boxes',
				'method' => 'remove_meta_boxes',
			),
			array(
				'hook' => 'manage_gistpen_posts_custom_column',
				'method' => 'manage_posts_custom_column',
				'args' => 2,
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
				'hook' => 'screen_layout_columns',
				'method' => 'screen_layout_columns',
			),
			array(
				'hook' => 'get_user_option_screen_layout_gistpen',
				'method' => 'screen_layout_gistpen',
			),
			array(
				'hook' => 'manage_gistpen_posts_columns',
				'method' => 'manage_posts_columns',
			),
			array(
				'hook' => 'posts_orderby',
				'method' => 'edit_screen_orderby',
				'args' => 2,
			),
		);
	}
}
