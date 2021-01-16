<?php
namespace Intraxia\Gistpen\Params;

use Intraxia\Gistpen\Model\Blob as BlobModel;
use Intraxia\Jaxion\Contract\Core\HasFilters;
use Intraxia\Jaxion\Contract\Axolotl\EntityManager;

/**
 * [Blob description]
 */
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
	 * @param array $data   Additional data.
	 *
	 * @return array
	 */
	public function apply_blob( $params, array $data = array() ) {
		/**
		 * Returned blob.
		 *
		 * @var BlobModel
		 */
		$blob = $this->em->find( BlobModel::class, get_the_ID(), array(
			'with' => 'language',
		) );

		if ( ! is_wp_error( $blob ) ) {
			$params['blob'] = $blob->serialize();

			if ( isset( $data['highlight'] ) ) {
				$params['blob']['highlight'] = $data['highlight'];
			}

			if ( isset( $data['offset'] ) ) {
				$params['blob']['offset'] = $data['offset'];
			}
		} else {
			$params['blobs'] = [
				'error'   => true,
				'message' => $blob->get_error_message(),
			];
		}

		return $params;
	}
	/**
	 * {@inheritDoc}
	 */
	public function filter_hooks() {
		return array(
			array(
				'hook'   => 'params.props.content.blob',
				'method' => 'apply_blob',
				'args'   => 2,
			),
		);
	}
}
