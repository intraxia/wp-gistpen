<?php
namespace Intraxia\Gistpen\Params;

use Intraxia\Gistpen\Database\EntityManager;
use Intraxia\Gistpen\Model\Blob as BlobModel;
use Intraxia\Jaxion\Contract\Core\HasFilters;

class Blob implements HasFilters {

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
	public function apply_prism( $params ) {
		/** @var BlobModel $blob */
		$blob = $this->em->find( EntityManager::BLOB_CLASS, get_the_ID(), array(
			'with' => 'language'
		) );

		if ( is_wp_error( $blob ) ) {
			// @todo
		} else {
			$params['blob'] = $blob->serialize();
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
				'method' => 'apply_prism',
			),
		);
	}
}
