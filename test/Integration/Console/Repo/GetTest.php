<?php
namespace Intraxia\Gistpen\Test\Integration\Console\Repo;

use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Console\Command\Repo as RepoCommand;
use Intraxia\Jaxion\Contract\Axolotl\EntityManager as EM;

class GetTest extends TestCase {
	public function test_should_display_repo() {
		$this->cli->shouldReceive( 'line' );
		$repo = $this->fm->create( Repo::class );

		$result = $this->command->get( [ $repo->ID ], [] );

		$this->assertTrue( $result );
	}

	public function test_should_use_fields_array() {
		$this->cli->shouldReceive( 'line' );
		$repo = $this->fm->create( Repo::class );

		$result = $this->command->get( [ $repo->ID ], [
			'fields' => [ 'description', 'slug' ],
		] );

		$this->assertTrue( $result );
	}

	public function test_should_use_fields_string() {
		$this->cli->shouldReceive( 'line' );
		$repo = $this->fm->create( Repo::class );

		$result = $this->command->get( [ $repo->ID ], [
			'fields' => 'description,slug',
		] );

		$this->assertTrue( $result );
	}
}
