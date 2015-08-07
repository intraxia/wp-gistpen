<?php
namespace Intraxia\Gistpen\Register;

/**
 * Registers the data types in WordPress
 *
 * @package    Intraxia\Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Data {

	/**
	 * Action hooks for Data service
	 *
	 * @var array
	 */
	public $actions = array(
		array(
			'hook' => 'init',
			'method' => 'post_type_gistpen',
		),
		array(
			'hook' => 'init',
			'method' => 'taxonomy_language',
		),
	);

	/**
	 * Register the gistpen post_type
	 *
	 * @since    0.1.0
	 */
	public function post_type_gistpen() {
		$labels = array(
			'name'                => _x( 'Gistpens', 'Post Type General Name', 'wp-gistpen' ),
			'singular_name'       => _x( 'Gistpen', 'Post Type Singular Name', 'wp-gistpen' ),
			'menu_name'           => __( 'Gistpens', 'wp-gistpen' ),
			'parent_item_colon'   => __( 'Parent Gistpen:', 'wp-gistpen' ),
			'all_items'           => __( 'All Gistpens', 'wp-gistpen' ),
			'view_item'           => __( 'View Gistpen', 'wp-gistpen' ),
			'add_new_item'        => __( 'Add New Gistpen', 'wp-gistpen' ),
			'add_new'             => __( 'Add New', 'wp-gistpen' ),
			'edit_item'           => __( 'Edit Gistpen', 'wp-gistpen' ),
			'update_item'         => __( 'Update Gistpen', 'wp-gistpen' ),
			'search_items'        => __( 'Search Gistpens', 'wp-gistpen' ),
			'not_found'           => __( 'Gistpen not found', 'wp-gistpen' ),
			'not_found_in_trash'  => __( 'No Gistpens found in Trash', 'wp-gistpen' ),
		);
		$args = array(
			'label'                => __( 'gistpens', 'wp-gistpen' ),
			'description'          => __( 'A collection of code snippets.', 'wp-gistpen' ),
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
			'name'                       => _x( 'Languages', 'Taxonomy General Name', 'wp-gistpen' ),
			'singular_name'              => _x( 'Language', 'Taxonomy Singular Name', 'wp-gistpen' ),
			'menu_name'                  => __( 'Language', 'wp-gistpen' ),
			'all_items'                  => __( 'All Languages', 'wp-gistpen' ),
			'parent_item'                => __( 'Parent Language', 'wp-gistpen' ),
			'parent_item_colon'          => __( 'Parent Language:', 'wp-gistpen' ),
			'new_item_name'              => __( 'New Language', 'wp-gistpen' ),
			'add_new_item'               => __( 'Add New Language', 'wp-gistpen' ),
			'edit_item'                  => __( 'Edit Language', 'wp-gistpen' ),
			'update_item'                => __( 'Update Language', 'wp-gistpen' ),
			'separate_items_with_commas' => __( 'Separate language with commas', 'wp-gistpen' ),
			'search_items'               => __( 'Search languages', 'wp-gistpen' ),
			'add_or_remove_items'        => __( 'Add or remove language', 'wp-gistpen' ),
			'choose_from_most_used'      => __( 'Choose from the most used languages', 'wp-gistpen' ),
			'not_found'                  => __( 'Not Found', 'wp-gistpen' ),
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
