<?php
namespace Intraxia\Gistpen\Test\Unit\Http\Filter;

use Intraxia\Gistpen\Database\EntityManager;
use Intraxia\Gistpen\Http\Filter\Repo;
use Intraxia\Gistpen\Test\Unit\TestCase;
use Mockery;
use Mockery\MockInterface;
use WP_Error;

class RepoFilterTest extends TestCase {
	public function setUp() {
		parent::setUp();

		$this->filter = new Repo;
	}

	public function test_should_invalidate_non_array() {
		$this->assertWPError( $this->filter->sanitize_blobs( '' ) );
	}

	public function test_should_validate_empty_array() {
		$this->assertNotWPError( $this->filter->sanitize_blobs( array() ) );
	}

	public function test_should_invalidate_empty_filename() {
		$this->assertWPError( $this->filter->sanitize_blobs( array(
			array(
				'filename' => '',
			)
		) ) );
	}
}
