<?php
namespace Intraxia\Gistpen\Test\Integration\Console\Blob;

use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Model\Blob;

class UpdateTest extends TestCase {
	public function test_should_add_to_existing_repo() {
		$this->cli->shouldReceive( 'success' )->once();
		$repo      = $this->fm->create( Repo::class );
		$fake_blob = $this->fm->instance( Blob::class, [ 'repo_id' => $repo->ID ] );

		$id = $this->command->create( [], [
			'filename' => $fake_blob->filename,
			'code'     => $fake_blob->code,
			'language' => $fake_blob->language->slug,
			'repo_id'  => $repo->ID,
		] );

		$db_blob = $this->em->find( Blob::class, $id, [
			'with' => 'language',
		] );

		$this->assertSame( $fake_blob->filename, $db_blob->filename );
		$this->assertSame( $fake_blob->code, $db_blob->code );
		$this->assertSame( $fake_blob->language->slug, $db_blob->language->slug );
		$this->assertSame( $fake_blob->repo_id, $db_blob->repo_id );
	}
}
