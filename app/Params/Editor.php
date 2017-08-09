<?php
namespace Intraxia\Gistpen\Params;

use Intraxia\Gistpen\Config;
use Intraxia\Gistpen\Database\EntityManager;
use Intraxia\Gistpen\Model\Blob as BlobModel;
use Intraxia\Gistpen\Model\Repo as RepoModel;
use Intraxia\Gistpen\Options\User;
use Intraxia\Jaxion\Contract\Core\HasFilters;

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
	 * @inheritDoc
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
		/** @var RepoModel $repo */
		$repo = $this->em->find( EntityManager::REPO_CLASS, get_the_ID(), array(
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

			$repo->blobs->add( new BlobModel( array(
				'filename' => '',
				'code' => '',
				'language' => $this->em->find_by( EntityManager::LANGUAGE_CLASS, array( 'slug' => 'plaintext' ) )->at( 0 ),
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
			'widths'     => array( '1', '2', '4', '8' ),
			'themes'     => array(
				'default'                         => __( 'Default', 'wp-gistpen' ),
				'dark'                            => __( 'Dark', 'wp-gistpen' ),
				'funky'                           => __( 'Funky', 'wp-gistpen' ),
				'okaidia'                         => __( 'Okaidia', 'wp-gistpen' ),
				'tomorrow'                        => __( 'Tomorrow', 'wp-gistpen' ),
				'twilight'                        => __( 'Twilight', 'wp-gistpen' ),
				'coy'                             => __( 'Coy', 'wp-gistpen' ),
				'cb'                              => __( 'CB', 'wp-gistpen' ),
				'ghcolors'                        => __( 'GHColors', 'wp-gistpen' ),
				'pojoaque'                        => __( 'Projoaque', 'wp-gistpen' ),
				'xonokai'                         => __( 'Xonokai', 'wp-gistpen' ),
				'base16-ateliersulphurpool-light' => __( 'Ateliersulphurpool-Light', 'wp-gistpen' ),
				'hopscotch'                       => __( 'Hopscotch', 'wp-gistpen' ),
				'atom-dark'                       => __( 'Atom Dark', 'wp-gistpen' ),
			),
			'statuses'   => get_post_statuses(),
			'languages'  => $languages['list'],
			'optionsOpen' => true,
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
