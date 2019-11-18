<?php
namespace Intraxia\Gistpen\Test;

use Intraxia\Gistpen\App;
use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Model\Language;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Jaxion\Core\UndefinedAliasException;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;
use Mockery;
use WP_UnitTestCase;

abstract class TestCase extends WP_UnitTestCase {
	/**
	 * @var Factory
	 */
	protected $factory;

	/**
	* @var FactoryMuffin
	*/
	protected $fm;

	/**
	 * @var App
	 */
	protected $app;

	public function setUp() {
		parent::setUp();

		$this->app     = App::instance();
		$this->factory = new Factory;
		$this->fm      = new FactoryMuffin(
			new MuffinStore( $this->app->fetch( 'database' ) )
		);

		$language_slugs = array_keys(
			$this->app->fetch( 'config' )->get_config_json( 'languages' )['list']
		);

		$definitions = [
			Repo::class => [
				'description' => Faker::sentence(),
				'slug'        => Faker::slug(),
				'status'      => Faker::randomElement( array_keys( get_post_statuses() ) ),
				'password'    => function() {
					return '';
				},
				'gist_id'     => function() {
					return '';
				},
				'sync'        => Faker::randomElement( [ 'on', 'off' ] ),
				'created_at'  => Faker::iso8601(),
				'updated_at'  => Faker::iso8601(),
			],
			Language::class => [
				'slug'     => Faker::randomElement( $language_slugs ),
			],
			Blob::class => [
				'filename' => function() {
					$faker = Faker::instance()->getGenerator();

					return $faker->word . '.' . $faker->fileExtension;
				},
				'code'     => Faker::text(),
				'language' => 'entity|' . Language::class,
			],
		];

		foreach ( $definitions as $class => $definition ) {
			$this->fm->define( $class )
				->setMaker( function () use ( $class ) {
					$model = new $class();
					$model->unguard();

					return $model;
				} )
				->setDefinitions( $definition )
				->setCallback( function ( $model ) {
					$model->reguard();
					$model->sync_original();
				} );
		}
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();
	}

	public function set_role( $role ) {
		$post = $_POST;
		$user_id = $this->factory->user->create( array( 'role' => $role ) );
		wp_set_current_user( $user_id );
		$_POST = array_merge( $_POST, $post );
	}
}
