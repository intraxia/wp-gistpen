<?php
namespace Intraxia\Gistpen\Test\Unit\Params;

use Intraxia\Gistpen\Test\Unit\TestCase;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Params\Editor;

class EditorTest extends TestCase {
	/**
	 * @var Editor
	 */
	public $editor;

	public function setUp() {
		parent::setUp();

		$this->editor = $this->app->make( Editor::class );
	}
	public function test_should_add_editor_params() {
		global $post;
		$repo   = $this->fm->create( Repo::class );
		$post   = $repo->get_underlying_wp_object(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$params = $this->editor->apply_editor( [] );

		$this->assertSame( $params['editor'], [
			'description' => $repo->description,
			'status'      => $repo->status,
			'password'    => $repo->password,
			'gist_id'     => $repo->gist_id,
			'sync'        => $repo->sync,
			'instances'   => [],
			'width'       => '2',
			'theme'       => 'default',
			'invisibles'  => 'off',
			'tabs'        => 'off',
			'errors'      => [],
		] );
	}
}
