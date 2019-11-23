<?php
namespace Intraxia\Gistpen\Test\Unit\Http\Filter;

use Intraxia\Gistpen\Database\EntityManager;
use Intraxia\Gistpen\Http\Filter\RepoCreate;
use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Test\Unit\TestCase;
use Mockery;
use Mockery\MockInterface;
use WP_Error;

class RepoCreateTest extends TestCase {
	public function setUp() {
		parent::setUp();

		$this->filter = new RepoCreate;
	}

	public function test_should_invalidate_non_string_description() {
		$this->assertWPError( $this->filter->sanitize_description( 123 ) );
	}

	public function test_should_validate_empty_description() {
		$this->assertNotWPError( $this->filter->sanitize_description( '' ) );
	}

	public function test_should_validate_description() {
		$repo = $this->fm->instance( Repo::class );
		$this->assertNotWPError( $this->filter->sanitize_description( $repo->description ) );
	}

	public function test_should_invalidate_invalid_status() {
		$this->assertWPError( $this->filter->sanitize_status( 123 ) );
		$this->assertWPError( $this->filter->sanitize_status( '' ) );
		$this->assertWPError( $this->filter->sanitize_status( 'nope' ) );
	}

	public function test_should_validate_valid_status() {
		$repo = $this->fm->instance( Repo::class );
		$this->assertNotWPError( $this->filter->sanitize_status( $repo->status ) );
	}

	public function test_should_invalidate_invalid_sync() {
		$this->assertWPError( $this->filter->sanitize_sync( 123 ) );
		$this->assertWPError( $this->filter->sanitize_sync( '' ) );
		$this->assertWPError( $this->filter->sanitize_sync( 'nope' ) );
	}

	public function test_should_validate_valid_sync() {
		$repo = $this->fm->instance( Repo::class );
		$this->assertNotWPError( $this->filter->sanitize_sync( $repo->sync ) );
	}

	public function test_should_invalidate_non_array_blobs() {
		$this->assertWPError( $this->filter->sanitize_blobs( '' ) );
	}

	public function test_should_validate_empty_array_blobs() {
		$this->assertNotWPError( $this->filter->sanitize_blobs( [] ) );
	}

	public function test_should_invalidate_missing_filename_on_blob() {
		$this->assertWPError( $this->filter->sanitize_blob( [], 0 ) );
	}

	public function test_should_invalidate_empty_filename_on_blob() {
		$this->assertWPError( $this->filter->sanitize_blob( [
			'filename' => '',
		], 0 ) );
	}

	public function test_should_invalidate_non_string_filename_on_blob() {
		$this->assertWPError( $this->filter->sanitize_blob( [
			'filename' => 123,
		], 0 ) );
	}

	public function test_should_invalidate_missing_code_on_blob() {
		$blob = $this->fm->instance( Blob::class );
		$this->assertWPError( $this->filter->sanitize_blob( [
			'filename' => $blob->filename,
		], 0 ) );
	}

	public function test_should_invalidate_non_string_code_on_blob() {
		$blob = $this->fm->instance( Blob::class );
		$this->assertWPError( $this->filter->sanitize_blob( [
			'filename' => $blob->filename,
			'code'     => 123,
		], 0 ) );
	}

	public function test_should_remove_extra_properties_and_set_defaults_on_blob() {
		$blob = $this->fm->instance( Blob::class );
		$this->assertSame( [
				'filename' => $blob->filename,
				'code'     => $blob->code,
				'language' => null,
			],
			$this->filter->sanitize_blob( [
				'filename' => $blob->filename,
				'code'     => $blob->code,
				'extra'    => 'value',
			], 0 )
		);
	}
}
