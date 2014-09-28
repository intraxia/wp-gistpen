<?php
/**
 * @group  updater
 */
class WP_Gistpen_Updater_Test extends WP_Gistpen_UnitTestCase {

	public $posts;
	public $gistpens;

	function setUp() {
		parent::setUp();
	}

	function set_up_0_4_0_test_posts() {
		register_post_type( 'gistpens', array() );
		register_taxonomy( 'language', array( 'gistpens' ) );

		foreach( WP_Gistpen_Language::$supported as $lang => $slug ) {
			$result = wp_insert_term( $lang, 'language', array( 'slug' => $slug ) );
			if( is_wp_error( $result ) ) {
				// @todo write error message?
			}
		}

		$terms = get_terms( 'language', 'hide_empty=0' );

		foreach ($terms as $term) {
			$languages[] = $term->term_id;
		}
		$num_posts = count( $languages );
		$this->gistpens = $this->factory->post->create_many( $num_posts, array(
			'post_type' => 'gistpens',
		), array(
			'post_title' => new WP_UnitTest_Generator_Sequence( 'Post title %s' ),
			'post_name' => new WP_UnitTest_Generator_Sequence( 'Post title %s' ),
			'post_content' => new WP_UnitTest_Generator_Sequence( 'Post content %s' )
		));

		foreach ( $this->gistpens as $gistpen_id ) {
			// Pick a random language
			$num_posts = $num_posts - 1;
			$lang_num = rand( 0, ( $num_posts ) );

			// Get the language's id
			$lang_id = $languages[$lang_num];

			// Remove the language and reindex the languages array
			unset( $languages[$lang_num] );
			$languages = array_values( $languages );

			// Give the post a description
			update_post_meta( $gistpen_id, '_wpgp_gistpen_description', 'This is a description of the Gistpen.' );

			// Give the post the language
			wp_set_object_terms( $gistpen_id, $lang_id, 'language', false );

			// Create and set up the user
			$user_id = $this->factory->user->create(array( 'role' => 'administrator' ) );
			wp_set_current_user( $user_id );
		}
	}

	function test_update_to_0_4_0() {

		$this->set_up_0_4_0_test_posts();

		WP_Gistpen_Updater::update_to_0_4_0();

		foreach ( $this->gistpens as $gistpen_id ) {
			$post = get_post( $gistpen_id );

			// The post should have no content
			$this->assertEmpty( $post->post_content );

			// Post type should now be gistpen
			$this->assertEquals( 'gistpen', $post->post_type );

			// The post should have no description
			$this->assertEmpty( get_post_meta( $post->ID, '_wpgp_gistpen_description', true ) );

			// The post should have no language
			$this->assertEmpty( wp_get_object_terms( $post->ID, 'language' ) );
			$this->assertEmpty( wp_get_object_terms( $post->ID, 'wpgp_language' ) );

			// The post title should be "This is a decription of the Gistpen."
			$this->assertEquals( 'This is a description of the Gistpen.', $post->post_title );

			$children = get_posts( array(
				'post_parent' => $gistpen_id,
				'post_type' => 'gistpen',
				'post_status' => 'any'
			) );

			// The post should have one child post
			$this->assertCount( 1, $children );

			$child = array_pop( $children );

			// The child post should have content
			$this->assertContains( 'Post content', $child->post_content );

			// The child post should have the correct filename
			$this->assertEmpty( $child->post_title );
			$this->assertContains( 'post-title', $child->post_name );

			// The child should be a gistpen
			$this->assertEquals( 'gistpen', $child->post_type );

			// The child post should have a language
			$language = wp_get_object_terms( $child->ID, 'wpgp_language' );
			$this->assertCount( 1, $language );
		}

		$posts = get_posts( array(
			'post_type' => 'gistpens',
			'posts_per_page' => -1,
			'post_status' => 'any'
		) );

		// There should be no gistpens left behind
		$this->assertCount( 0, $posts );

		$terms = get_terms( 'language', 'hide_empty=0' );

		// There should be no language terms left behind
		$this->assertCount( 0, $terms );
	}

	function tearDown() {
		parent::tearDown();
	}
}

