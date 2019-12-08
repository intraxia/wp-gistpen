<?php
namespace Intraxia\Gistpen\Test\Integration\Console\Repo;

use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Console\Command\Repo as RepoCommand;
use Intraxia\Jaxion\Contract\Axolotl\EntityManager as EM;

class UpdateTest extends TestCase {
	public function test_should_update_repo_from_args() {
		$orig_repo = $this->fm->create( Repo::class );
		$fake_repo = $this->fm->instance( Repo::class );
		$this->cli->shouldReceive( 'success' )->once();
		$id = $this->command->update( [ $orig_repo->ID ], [
			'status'   => $fake_repo->status,
			'password' => $fake_repo->password,
			'sync'     => $fake_repo->sync,
		] );

		$db_repo = $this->em->find( Repo::class, $id );

		$this->assertSame( $db_repo->description, $orig_repo->description );
		$this->assertSame( $db_repo->status, $fake_repo->status );
		$this->assertSame( $db_repo->password, $fake_repo->password );
		$this->assertSame( $db_repo->sync, $fake_repo->sync );
	}

	public function test_should_output_error_on_find_before_update_error() {
		$this->app->set( EM::class, $em = \Mockery::mock( EM::class ) );
		$this->command = $this->app->make( RepoCommand::class );

		$orig_repo = $this->fm->create( Repo::class );
		$fake_repo = $this->fm->instance( Repo::class );

		$em->shouldReceive( 'find' )->once()->andReturn( $error = new \WP_Error() );
		$this->cli->shouldReceive( 'error' )->once()->andReturn( $error );

		$result = $this->command->update( [ $orig_repo->ID ], [
			'status'   => $fake_repo->status,
			'password' => $fake_repo->password,
			'sync'     => $fake_repo->sync,
		] );

		$this->assertSame( $result, $error );
	}

	public function test_should_output_error_on_update_error() {
		$this->app->set( EM::class, $em = \Mockery::mock( EM::class ) );
		$this->command = $this->app->make( RepoCommand::class );

		$orig_repo = $this->fm->create( Repo::class );
		$fake_repo = $this->fm->instance( Repo::class );

		$em->shouldReceive( 'find' )->once()->andReturn( $orig_repo );
		$em->shouldReceive( 'persist' )->once()->andReturn( $error = new \WP_Error() );
		$this->cli->shouldReceive( 'error' )->once()->andReturn( $error );

		$result = $this->command->update( [ $orig_repo->ID ], [
			'status'   => $fake_repo->status,
			'password' => $fake_repo->password,
			'sync'     => $fake_repo->sync,
		] );

		$this->assertSame( $result, $error );
	}
}
