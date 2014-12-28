<?php
use WP_Gistpen\View\Editor;

/**
 * @group view
 */
class WP_Gistpen_View_Editor_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		global $post;

		parent::setUp();
		$this->editor = new Editor( WP_Gistpen::$plugin_name, WP_Gistpen::$version );

		$this->create_post_and_children();

		set_current_screen( 'gistpen' );

		$post = $this->gistpen;
	}

	function test_editor_div() {
		ob_start();
		$this->editor->render_editor_div();
		$html = ob_get_contents();
		ob_end_clean();

		$this->assertValidHTML( $html );
	}

	function tearDown() {
		parent::tearDown();
	}
}
