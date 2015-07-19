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
	 * Register the gistpen post_type
	 *
	 * @since    0.1.0
	 */
	public function post_type_gistpen() {
		$labels = array(
			'name'                => _x( 'Gistpens', 'Post Type General Name', \WP_Gistpen::$plugin_name ),
			'singular_name'       => _x( 'Gistpen', 'Post Type Singular Name', \WP_Gistpen::$plugin_name ),
			'menu_name'           => __( 'Gistpens', \WP_Gistpen::$plugin_name ),
			'parent_item_colon'   => __( 'Parent Gistpen:', \WP_Gistpen::$plugin_name ),
			'all_items'           => __( 'All Gistpens', \WP_Gistpen::$plugin_name ),
			'view_item'           => __( 'View Gistpen', \WP_Gistpen::$plugin_name ),
			'add_new_item'        => __( 'Add New Gistpen', \WP_Gistpen::$plugin_name ),
			'add_new'             => __( 'Add New', \WP_Gistpen::$plugin_name ),
			'edit_item'           => __( 'Edit Gistpen', \WP_Gistpen::$plugin_name ),
			'update_item'         => __( 'Update Gistpen', \WP_Gistpen::$plugin_name ),
			'search_items'        => __( 'Search Gistpens', \WP_Gistpen::$plugin_name ),
			'not_found'           => __( 'Gistpen not found', \WP_Gistpen::$plugin_name ),
			'not_found_in_trash'  => __( 'No Gistpens found in Trash', \WP_Gistpen::$plugin_name ),
		);
		$args = array(
			'label'                => __( 'gistpens', \WP_Gistpen::$plugin_name ),
			'description'          => __( 'A collection of code snippets.', \WP_Gistpen::$plugin_name ),
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
			'name'                       => _x( 'Languages', 'Taxonomy General Name', \WP_Gistpen::$plugin_name ),
			'singular_name'              => _x( 'Language', 'Taxonomy Singular Name', \WP_Gistpen::$plugin_name ),
			'menu_name'                  => __( 'Language', \WP_Gistpen::$plugin_name ),
			'all_items'                  => __( 'All Languages', \WP_Gistpen::$plugin_name ),
			'parent_item'                => __( 'Parent Language', \WP_Gistpen::$plugin_name ),
			'parent_item_colon'          => __( 'Parent Language:', \WP_Gistpen::$plugin_name ),
			'new_item_name'              => __( 'New Language', \WP_Gistpen::$plugin_name ),
			'add_new_item'               => __( 'Add New Language', \WP_Gistpen::$plugin_name ),
			'edit_item'                  => __( 'Edit Language', \WP_Gistpen::$plugin_name ),
			'update_item'                => __( 'Update Language', \WP_Gistpen::$plugin_name ),
			'separate_items_with_commas' => __( 'Separate language with commas', \WP_Gistpen::$plugin_name ),
			'search_items'               => __( 'Search languages', \WP_Gistpen::$plugin_name ),
			'add_or_remove_items'        => __( 'Add or remove language', \WP_Gistpen::$plugin_name ),
			'choose_from_most_used'      => __( 'Choose from the most used languages', \WP_Gistpen::$plugin_name ),
			'not_found'                  => __( 'Not Found', \WP_Gistpen::$plugin_name ),
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
