<?php
namespace Intraxia\Gistpen\Params;

use Intraxia\Gistpen\Database\EntityManager;
use Intraxia\Gistpen\Model\Blob as BlobModel;
use Intraxia\Gistpen\Model\Repo as RepoModel;
use Intraxia\Jaxion\Contract\Core\HasFilters;

class Repo implements HasFilters {

	/**
	 * Database service.
	 *
	 * @var EntityManager
	 */
	private $em;

	/**
	 * Prism constructor.
	 *
	 * @param EntityManager $em
	 *
	 * @internal param Site $site
	 */
	public function __construct( EntityManager $em ) {
		$this->em = $em;
	}

	/**
	 * Add prism key to params array.
	 *
	 * @param array $params Current params array.
	 *
	 * @return array
	 */
	public function apply_repo( $params ) {
		/** @var RepoModel $repo */
		$repo = $this->em->find( EntityManager::REPO_CLASS, get_the_ID(), array(
			'with' => array(
				'blobs' => array(
					'with' => 'language',
				),
			),
		) );

		if ( is_wp_error( $repo ) ) {
			// @todo
		} else {
			$params['repo'] = $repo->serialize();
		}

		return $params;
	}
	/**
	 * @inheritDoc
	 */
	public function filter_hooks() {
		return array(
			array(
				'hook'   => 'params.props.content.repo',
				'method' => 'apply_repo',
			),
			array(
				'hook'   => 'params.state.edit',
				'method' => 'apply_repo',
			),
		);
	}
}
