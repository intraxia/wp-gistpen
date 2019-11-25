<?php
namespace Intraxia\Gistpen\Test\Integration\Console\Repo;

use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Console\Command\Repo as RepoCommand;
use Intraxia\Jaxion\Contract\Axolotl\EntityManager as EM;

class CreateTest extends TestCase {
	public function test_should_create_repo_from_args() {
		$fake_repo = $this->fm->instance( Repo::class );
		$this->cli->shouldReceive( 'success' )->once();
		$id = $this->command->create( [], [
			'description' => $fake_repo->description,
			'status'      => $fake_repo->status,
			'password'    => $fake_repo->password,
			'sync'        => $fake_repo->sync,
		] );

		$db_repo = $this->em->find( Repo::class, $id );

		$this->assertSame( $db_repo->description, $fake_repo->description );
		$this->assertSame( $db_repo->status, $fake_repo->status );
		$this->assertSame( $db_repo->password, $fake_repo->password );
		$this->assertSame( $db_repo->sync, $fake_repo->sync );
	}

	public function test_should_output_line_with_porcelain_flag() {
		$fake_repo = $this->fm->instance( Repo::class );
		$this->cli->shouldReceive( 'line' )->once();
		$id = $this->command->create( [], [
			'description' => $fake_repo->description,
			'status'      => $fake_repo->status,
			'password'    => $fake_repo->password,
			'sync'        => $fake_repo->sync,
			'porcelain'   => true,
		] );

		$db_repo = $this->em->find( Repo::class, $id );

		$this->assertSame( $db_repo->description, $fake_repo->description );
	}

	public function test_should_output_error_on_create_error() {
		$this->app->set( EM::class, $this->em = \Mockery::mock( EM::class ) );
		$this->command = $this->app->make( RepoCommand::class );

		$this->em->shouldReceive( 'create' )->once()->andReturn( $error = new \WP_Error() );
		$this->cli->shouldReceive( 'error' )->once()->andReturn( $error );

		$result = $this->command->create( [], [] );

		$this->assertSame( $result, $error );
	}
}
