<?php
namespace Intraxia\Gistpen\Console\Command;

use Intraxia\Jaxion\Contract\Axolotl\EntityManager;
use Intraxia\Gistpen\Model\Repo as RepoModel;
use WP_CLI;
use function \WP_CLI\Utils\get_flag_value;

/**
 * Manages the gistpen's repos.
 *
 * ## EXAMPLES
 *
 *     # Create a new repo.
 *     $ wp gistpen repo create
 *     Success: Created repo 123.
 */
class Repo {

	/**
	 * EntityManager service.
	 *
	 * @var EntityManager
	 */
	private $em;

	/**
	 * WP_CLI service.
	 *
	 * @var WP_CLI
	 */
	private $cli;

	/**
	 * Constructor.
	 *
	 * @param EntityManager $em
	 * @param WP_CLI        $cli
	 */
	public function __construct( EntityManager $em, WP_CLI $cli ) {
		$this->em  = $em;
		$this->cli = $cli;
	}

	/**
	 * Creates a new repo.
	 *
	 * ## OPTIONS
	 *
	 * [--description=<description>]
	 * : Short description of the new repo. Defaults to an empty string.
	 *
	 * [--status=<status>]
	 * : Status of the new repo. Defaults to "draft".
	 *
	 * [--password=<password>]
	 * : Password for the new repo. Defaults to an empty string.
	 *
	 * [--sync=<sync>]
	 * : Whether to sync the repo to Gist. One of "on" or "off". Defaults to "off".
	 *
	 * [--porcelain]
	 * : Only output the repo ID. Useful for shell scripts.
	 *
	 * ## EXAMPLES
	 *
	 *     # Create new repo.
	 *     $ wp gistpen repo create --description="A new repo."
	 *     Success: Created repo 1234.
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function create( $args, $assoc_args ) {
		$repo = $this->em->create( RepoModel::class, [
			'description' => get_flag_value( $assoc_args, 'description', '' ),
			'status'      => get_flag_value( $assoc_args, 'status', 'draft' ),
			'password'    => get_flag_value( $assoc_args, 'password', '' ),
			// Use "on" if it matches, otherwise use "off". Ensures we don't get invalid values.
			'sync'        => get_flag_value( $assoc_args, 'sync', 'off' ) === 'on' ? 'on' : 'off',
		] );

		if ( is_wp_error( $repo ) ) {
			return WP_CLI::error( $repo );
		}

		if ( get_flag_value( $assoc_args, 'porcelain', false ) ) {
			WP_CLI::line( $repo->ID );
		} else {
			WP_CLI::success( sprintf(
				/* translators: %s: Repo ID. */
				__( 'Created repo %s.', 'wp-gistpen' ),
				$repo->ID
			) );
		}

		return $repo->ID;
	}

	/**
	 * Update an existing repo.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : ID of repo to update.
	 *
	 * [--description=<description>]
	 * : Short description of the new repo. Defaults to an empty string.
	 *
	 * [--status=<status>]
	 * : Status of the new repo. Defaults to "draft".
	 *
	 * [--password=<password>]
	 * : Password for the new repo. Defaults to an empty string.
	 *
	 * [--sync=<sync>]
	 * : Whether to sync the repo to Gist. One of "on" or "off". Defaults to "off".
	 *
	 * ## EXAMPLES
	 *
	 *     # Create new repo.
	 *     $ wp gistpen repo update 1234 --description="A new repo."
	 *     Success: Updated repo 1234.
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function update( $args, $assoc_args ) {
		$id   = $args[0];
		$repo = $this->em->find( RepoModel::class, $id );

		if ( is_wp_error( $repo ) ) {
			return WP_CLI::error( $repo );
		}

		$repo->merge( [
			'description' => get_flag_value( $assoc_args, 'description', $repo->description ),
			'status'      => get_flag_value( $assoc_args, 'status', $repo->status ),
			'password'    => get_flag_value( $assoc_args, 'password', $repo->pasword ),
			// Use "on" if it matches, otherwise use "off". Ensures we don't get invalid values.
			'sync'        => get_flag_value( $assoc_args, 'sync', $repo->sync ) === 'on' ? 'on' : 'off',
		] );

		$repo = $this->em->persist( $repo );

		if ( is_wp_error( $repo ) ) {
			return WP_CLI::error( $repo );
		}

		WP_CLI::success( sprintf(
			/* translators: %s: Repo ID. */
			__( 'Updated repo %s.', 'wp-gistpen' ),
			$repo->ID
		) );

		return $repo->ID;
	}

	/**
	 * Gets details about a repo.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The ID of the repo to get.
	 *
	 * [--field=<field>]
	 * : Instead of returning the whole repo, returns the value of a single field.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific fields. Defaults to all fields.
	 *
	 * [--format=<format>]
	 * : Render output in a particular format.
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - csv
	 *   - json
	 *   - yaml
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     # Save the post content to a file
	 *     $ wp gistpen repo get 123 --field=content > file.txt
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function get( $args, $assoc_args ) {
		$repo = $this->em->find( RepoModel::class, $args[0] );

		if ( is_wp_error( $repo ) ) {
			return WP_CLI::error( $repo );
		}

		$repo = $repo->serialize();
		// @TODO(mAAdhaTTah) Custom console serializer?
		unset( $repo['blobs'] );

		if ( empty( $assoc_args['fields'] ) ) {
			$assoc_args['fields'] = array_keys( $repo );
		} elseif ( is_string( $assoc_args['fields'] ) ) {
			$assoc_args['fields'] = explode( ',', $assoc_args['fields'] );
		}

		$fields = $assoc_args['fields'];
		unset( $assoc_args['fields'] );

		( new \WP_CLI\Formatter( $assoc_args, $fields ) )->display_item( $repo );

		return true;
	}

	/**
	 * Deletes an existing repo.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : ID of repo to delete.
	 *
	 * [--force]
	 * : Skip the trash bin.
	 *
	 * ## EXAMPLES
	 *
	 *     # Delete repo skipping trash
	 *     $ wp gistpen repo delete 123 --force
	 *     Success: Deleted repo 123.
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function delete( $args, $assoc_args ) {
		$repo = $this->em->find( RepoModel::class, $args[0], [
			'with' => [
				'blobs' => [
					'with' => [
						'language',
					],
				],
			],
		] );

		if ( is_wp_error( $repo ) ) {
			return WP_CLI::error( $repo );
		}

		$repo = $this->em->delete( $repo, get_flag_value( $assoc_args, 'force', false ) );

		if ( is_wp_error( $repo ) ) {
			return WP_CLI::error( $repo );
		}

		WP_CLI::success( sprintf(
			/* translators: %s: Repo ID. */
			__( 'Deleted repo %s.', 'wp-gistpen' ),
			$repo->ID
		) );

		return $repo->ID;
	}

	/**
	 * Verifies whether a repo exists.
	 *
	 * Displays a success message if the repo does exist.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The ID of the repo to check.
	 *
	 * ## EXAMPLES
	 *
	 *     # The repo exists.
	 *     $ wp repo exists 1
	 *     Success: Post with ID 1337 exists.
	 *     $ echo $?
	 *     0
	 *
	 *     # The repo does not exist.
	 *     $ wp gistpen repo exists 10000
	 *     $ echo $?
	 *     1
	 *
	 * @param array $args
	 */
	public function exists( $args ) {
		$repo = $this->em->find( RepoModel::class, $args[0] );

		if ( is_wp_error( $repo ) ) {
			WP_CLI::halt( 1 );

			return false;
		}

		WP_CLI::success( sprintf(
			/* translators: %s: Repo ID. */
			__( 'Repo with ID %s exists.', 'wp-gistpen' ),
			$repo->ID
		) );

		return true;
	}
}
