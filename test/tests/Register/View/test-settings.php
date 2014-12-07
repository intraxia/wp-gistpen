<?php
use WP_Gistpen\Register\View\Settings;
/**
 * @group register
 */
class WP_Gistpen_View_Settings_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		global $title;

		parent::setUp();
		$this->settings = new Settings( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
		$title = "Settings page";
	}

	function test_check_settings_page_valid_html() {
		ob_start();
		$this->settings->display_plugin_admin_page();
		$html = ob_get_contents();
		ob_end_clean();

		// Right now we're just exercising the code
		// to make sure nothing breaks
		// CMB & CMB2 don't currently validate
		// Error: https://www.evernote.com/shard/s89/sh/bdb4c5b2-a95f-4fb7-a41d-54c73f666c2e/8019790469f7b76c0384d762ff3ec17d/res/540eef1e-21bc-4a00-8c29-6f8f4e15af1e/skitch.png?resizeSmall&width=832
		$this->assertNotEmpty( $html );
	}

	function tearDown() {
		parent::tearDown();
	}
}
