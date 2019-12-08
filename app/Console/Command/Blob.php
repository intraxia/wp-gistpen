<?php
namespace Intraxia\Gistpen\Console\Command;

use Intraxia\Jaxion\Contract\Axolotl\EntityManager;
use Intraxia\Gistpen\Model\Blob as BlobModel;
use Intraxia\Gistpen\Model\Repo as RepoModel;
use WP_CLI;
use function \WP_CLI\Utils\get_flag_value;

/**
 * Manages the gistpen's blobs.
 *
 * ## EXAMPLES
 *
 *     # Create a new blob.
 *     $ wp gistpen blob create --repo_id=1234 --filename="dummy.txt"
 *     Success: Created blob 5678.
 */
class Blob extends Base {

	/**
	 * EntityManager service.
	 *
	 * @var EntityManager
	 */
	protected $em;

	/**
	 * Constructor.
	 *
	 * @param EntityManager $em
	 */
	public function __construct( EntityManager $em ) {
		$this->em = $em;
	}

	/**
	 * Creates a new blob.
	 *
	 * ## OPTIONS
	 *
	 * --filename=<filename>
	 * : Blob filename. Cannot be empty.
	 *
	 * [--code=<code>]
	 * : Code for the snippet. Defaults to an empty string.
	 *
	 * --repo_id=<repo_id>
	 * : Blob ID to attach the blob to. Must be a valid repo.
	 *
	 * [--language=<language>]
	 * : Language of the blob. Defaults to "plaintext".
	 *
	 * [<file>]
	 * : Read blob code from <file>. If this value is present, the
	 *     `--code` argument will be ignored.
	 *
	 *   Passing `-` as the filename will cause blob code to
	 *   be read from STDIN.
	 *
	 * [--porcelain]
	 * : Only output the blob ID. Useful for shell scripts.
	 *
	 * ## EXAMPLES
	 *
	 *     # Create new blob.
	 *     $ wp gistpen blob create --filename="file.js" --language="js" --repo_id="1234"
	 *     Success: Created blob 5678.
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function create( $args, $assoc_args ) {
		if ( ! empty( $args[0] ) ) {
			$assoc_args['code'] = $this->read_from_file_or_stdin( $args[0] );
		}

		$repo = $this->em->find( RepoModel::class, $assoc_args['repo_id'] );

		if ( is_wp_error( $repo ) ) {
			return WP_CLI::error( $repo );
		}

		$blob = $this->em->create( BlobModel::class, [
			'filename' => $assoc_args['filename'],
			'code'     => get_flag_value( $assoc_args, 'code', '' ),
			'language' => [
				// @TODO(mAAdhaTTah) this is a bad API for the EntityManager.
				'slug' => get_flag_value( $assoc_args, 'language', 'plaintext' ),
			],
			'repo_id'  => $repo->ID,
		], [ 'unguarded' => true ] );

		if ( is_wp_error( $blob ) ) {
			return WP_CLI::error( $blob );
		}

		if ( get_flag_value( $assoc_args, 'porcelain', false ) ) {
			WP_CLI::line( $blob->ID );
		} else {
			WP_CLI::success( sprintf(
				/* translators: %s: Blob ID. */
				__( 'Created blob %s.', 'wp-gistpen' ),
				$blob->ID
			) );
		}

		return $blob->ID;
	}

	/**
	 * Update an existing blob.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : ID of blob to update.
	 *
	 * [--filename=<filename>]
	 * : Blob filename.
	 *
	 * [--code=<code>]
	 * : Code for the snippet. Defaults to an empty string.
	 *
	 * [--language=<language>]
	 * : Language of the blob. Defaults to plaintext.
	 *
	 * [<file>]
	 * : Read blob code from <file>. If this value is present, the
	 *     `--code` argument will be ignored.
	 *
	 *   Passing `-` as the filename will cause blob code to
	 *   be read from STDIN.
	 *
	 * ## EXAMPLES
	 *
	 *     # Create new blob.
	 *     $ wp gistpen blob update 1234 --filename="new-dummy.js"
	 *     Success: Updated repo 1234.
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function update( $args, $assoc_args ) {
		if ( ! empty( $args[1] ) ) {
			$assoc_args['code'] = $this->read_from_file_or_stdin( $args[1] );
		}

		$blob = $this->em->find( BlobModel::class, $args[0] );

		if ( is_wp_error( $blob ) ) {
			return WP_CLI::error( $blob );
		}

		$blob->merge( [
			'filename' => get_flag_value( $assoc_args, 'filename', $blob->filename ),
			'code'     => get_flag_value( $assoc_args, 'code', $blob->code ),
			'language' => get_flag_value( $assoc_args, 'language', $blob->language ),
		] );

		$blob = $this->em->persist( $blob );

		if ( is_wp_error( $blob ) ) {
			return WP_CLI::error( $blob );
		}

		if ( get_flag_value( $assoc_args, 'porcelain', false ) ) {
			WP_CLI::line( $blob->ID );
		} else {
			WP_CLI::success( sprintf(
				/* translators: %s: Blob ID. */
				__( 'Updated blob %s.', 'wp-gistpen' ),
				$blob->ID
			) );
		}

		return $blob->ID;
	}

	/**
	 * Gets details about a blob.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The ID of the blob to get.
	 *
	 * [--field=<field>]
	 * : Instead of returning the whole blob, returns the value of a single field.
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
	 *     # Save the blob snippet to a file.
	 *     $ wp gistpen blob get 123 --field=code > $(wp gistpen blob get 123 --field=filename)
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function get( $args, $assoc_args ) {
		$blob = $this->em->find( BlobModel::class, $args[0], [
			'with' => [
				'language' => [],
			],
		] );

		if ( is_wp_error( $blob ) ) {
			return WP_CLI::error( $blob );
		}

		$blob = $blob->serialize();
		// @TODO(mAAdhaTTah) Custom console serializer?
		$blob['language'] = $blob['language']['display_name'];

		if ( empty( $assoc_args['fields'] ) ) {
			$assoc_args['fields'] = array_keys( $blob );
		} elseif ( is_string( $assoc_args['fields'] ) ) {
			$assoc_args['fields'] = explode( ',', $assoc_args['fields'] );
		}

		$fields = $assoc_args['fields'];
		unset( $assoc_args['fields'] );

		( new \WP_CLI\Formatter( $assoc_args, $fields ) )->display_item( $blob );
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
		$blob = $this->em->find( BlobModel::class, $args[0] );

		if ( is_wp_error( $blob ) ) {
			return WP_CLI::error( $blob );
		}

		$blob = $this->em->delete( $blob, get_flag_value( $assoc_args, 'force', false ) );

		if ( is_wp_error( $blob ) ) {
			return WP_CLI::error( $blob );
		}

		WP_CLI::success( sprintf(
			/* translators: %s: Blob ID. */
			__( 'Deleted blob %s.', 'wp-gistpen' ),
			$blob->ID
		) );

		return $blob->ID;
	}

	/**
	 * Verifies whether a blob exists.
	 *
	 * Displays a success message if the blob does exist.
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
		$blob = $this->em->find( BlobModel::class, $args[0] );

		if ( is_wp_error( $blob ) ) {
			WP_CLI::halt( 1 );

			return false;
		}

		WP_CLI::success( sprintf(
			/* translators: %s: Blob ID. */
			__( 'Blob with ID %s exists.', 'wp-gistpen' ),
			$blob->ID
		) );

		return true;
	}
}
