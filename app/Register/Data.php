<?php
namespace WP_Gistpen\Register;

/**
 * Registers the data types in WordPress
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Data {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 * @var      string    $plugin_name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the gistpen post_type
	 *
	 * @since    0.1.0
	 */
	public function post_type_gistpen() {
		$labels = array(
			'name'                => _x( 'Gistpens', 'Post Type General Name', $this->plugin_name ),
			'singular_name'       => _x( 'Gistpen', 'Post Type Singular Name', $this->plugin_name ),
			'menu_name'           => __( 'Gistpens', $this->plugin_name ),
			'parent_item_colon'   => __( 'Parent Gistpen:', $this->plugin_name ),
			'all_items'           => __( 'All Gistpens', $this->plugin_name ),
			'view_item'           => __( 'View Gistpen', $this->plugin_name ),
			'add_new_item'        => __( 'Add New Gistpen', $this->plugin_name ),
			'add_new'             => __( 'Add New', $this->plugin_name ),
			'edit_item'           => __( 'Edit Gistpen', $this->plugin_name ),
			'update_item'         => __( 'Update Gistpen', $this->plugin_name ),
			'search_items'        => __( 'Search Gistpens', $this->plugin_name ),
			'not_found'           => __( 'Gistpen not found', $this->plugin_name ),
			'not_found_in_trash'  => __( 'No Gistpens found in Trash', $this->plugin_name ),
		);
		$args = array(
			'label'                => __( 'gistpens', $this->plugin_name ),
			'description'          => __( 'A collection of code snippets.', $this->plugin_name ),
			'labels'               => $labels,
			'supports'             => array( 'author', 'comments', 'revisions' ),
			'taxonomies'           => array( 'post_tag', 'wpgp_language' ),
			'hierarchical'         => true,
			'public'               => true,
			'show_ui'              => true,
			'show_in_menu'         => true,
			'show_in_nav_menus'    => true,
			'show_in_admin_bar'    => true,
			'menu_position'        => 5,
			'can_export'           => true,
			'has_archive'          => true,
			'exclude_from_search'  => false,
			'publicly_queryable'   => true,
			'capability_type'      => 'post',
			'menu_icon'            => 'dashicons-editor-code',
			'rewrite'              => array(
				'slug'               => 'gistpens',
				'with_front'         => true,
			)
		);
		register_post_type( 'gistpen', $args );
	}

	/**
	 * Register the language taxonomy
	 *
	 * @since    0.1.0
	 */
	public function taxonomy_language() {

		$labels = array(
			'name'                       => _x( 'Languages', 'Taxonomy General Name', $this->plugin_name ),
			'singular_name'              => _x( 'Language', 'Taxonomy Singular Name', $this->plugin_name ),
			'menu_name'                  => __( 'Language', $this->plugin_name ),
			'all_items'                  => __( 'All Languages', $this->plugin_name ),
			'parent_item'                => __( 'Parent Language', $this->plugin_name ),
			'parent_item_colon'          => __( 'Parent Language:', $this->plugin_name ),
			'new_item_name'              => __( 'New Language', $this->plugin_name ),
			'add_new_item'               => __( 'Add New Language', $this->plugin_name ),
			'edit_item'                  => __( 'Edit Language', $this->plugin_name ),
			'update_item'                => __( 'Update Language', $this->plugin_name ),
			'separate_items_with_commas' => __( 'Separate language with commas', $this->plugin_name ),
			'search_items'               => __( 'Search languages', $this->plugin_name ),
			'add_or_remove_items'        => __( 'Add or remove language', $this->plugin_name ),
			'choose_from_most_used'      => __( 'Choose from the most used languages', $this->plugin_name ),
			'not_found'                  => __( 'Not Found', $this->plugin_name ),
		);
		$capabilities = array(
			'manage_terms'               => 'noone',
			'edit_terms'                 => 'noone',
			'delete_terms'               => 'noone',
			'assign_terms'               => 'edit_posts',
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => false,
			'public'                     => true,
			'show_ui'                    => false,
			'show_admin_column'          => false,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => false,
			'required'                   => true,
			'capabilities'               => $capabilities,
		);

		register_taxonomy( 'wpgp_language', array( 'gistpen' ), $args );

	}
}
