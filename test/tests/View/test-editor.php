<?php
use Intraxia\Gistpen\View\Editor;

/**
 * @group view
 */
class View_Editor_Test extends \Intraxia\Gistpen\Test\UnitTestCase {

	function setUp() {
		global $post;

		parent::setUp();
		$app = Intraxia\Gistpen\App::get();
		$this->editor = new Editor( $app['path'] );

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
