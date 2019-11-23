<?php
namespace Intraxia\Gistpen\Params;

use Intraxia\Jaxion\Core\Config;
use Intraxia\Gistpen\Database\EntityManager;
use Intraxia\Gistpen\Model\Blob as BlobModel;
use Intraxia\Gistpen\Model\Klass;
use Intraxia\Gistpen\Model\Language;
use Intraxia\Gistpen\Model\Repo as RepoModel;
use Intraxia\Gistpen\Options\User;
use Intraxia\Jaxion\Axolotl\Model;
use Intraxia\Jaxion\Contract\Core\HasFilters;
use stdClass;
use WP_Term;

/**
 * Service for managing the editor slice of state.
 */
class Editor implements HasFilters {

	/**
	 * App Config service.
	 *
	 * @var Config
	 */
	private $config;

	/**
	 * Database service.
	 *
	 * @var EntityManager
	 */
	private $em;

	/**
	 * User options service.
	 *
	 * @var User
	 */
	private $user;

	/**
	 * Construct a new service.
	 *
	 * @param Config        $config Configuration service.
	 * @param EntityManager $em     EntityManager service.
	 * @param User          $user   User service.
	 */
	public function __construct( Config $config, EntityManager $em, User $user ) {
		$this->config = $config;
		$this->em = $em;
		$this->user = $user;
	}

	/**
	 * Apply the editor params to the state array.
	 *
	 * @param array $params
	 *
	 * @return mixed
	 */
	public function apply_editor( $params ) {
		/**
		 * Returned model.
		 *
		 * @var RepoModel
		 */
		$repo = $this->em->find( Klass::REPO, get_the_ID(), array(
			'with' => array(
				'blobs' => array(
					'with' => 'language',
				),
			),
		) );

		// @todo move to accessors?
		if ( 'auto-draft' === $repo->status ) {
			$repo->status = 'draft';
			$repo->description = '';
			$repo->sync = 'off';

			$language = $this->em->find_by( Klass::LANGUAGE, array( 'slug' => 'plaintext' ) );

			if ( $language->index_exists( 0 ) ) {
				$language = $language->at( 0 );
			} else {
				$term       = new WP_Term( new stdClass );
				$term->slug = 'plaintext';

				$language = new Language( array( Model::OBJECT_KEY => $term ) );
			}

			$repo->blobs->add( new BlobModel( array(
				'filename' => '',
				'code' => '',
				'language' => $language,
			) ) );
		}

		$blobs = iterator_to_array( $repo->blobs );
		usort( $blobs, function( $a, $b ) {
			return (int) $a->ID - (int) $b->ID;
		} );

		$languages = $this->config->get_config_json( 'languages' );

		$params['editor'] = array(
			'description' => $repo->description,
			'status' => $repo->status,
			'password' => $repo->password,
			'gist_id' => $repo->gist_id,
			'sync' => $repo->sync,
			'instances'  => array_map( function ( BlobModel $blob ) {
				return array(
					'key'      => (string) $blob->ID ? : 'new0',
					'filename' => $blob->filename,
					'code'     => $blob->code,
					'language' => $blob->language->slug,
					'cursor'   => false,
					'history'  => array(
						'undo' => array(),
						'redo' => array(),
					),
				);
			}, $blobs ),
			'width'      => $this->user->get( 'editor.indent_width' ),
			'theme'      => $this->user->get( 'editor.theme' ),
			'invisibles' => $this->user->get( 'editor.invisibles_enabled' ) ? : 'off',
			'tabs'       => $this->user->get( 'editor.tabs_enabled' ) ? : 'off',
			'errors'     => array(),
		);

		return $params;
	}

	/**
	 * Provides the array of filters the class wants to register with WordPress.
	 *
	 * These filters are retrieved by the Loader class and used to register the
	 * correct service methods with WordPress.
	 *
	 * @return array[]
	 */
	public function filter_hooks() {
		return array(
			array(
				'hook'   => 'params.state.edit',
				'method' => 'apply_editor',
			),
			array(
				'hook'   => 'params.props.edit',
				'method' => 'apply_editor',
			),
		);
	}
}
