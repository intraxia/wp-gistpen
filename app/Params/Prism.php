<?php
namespace Intraxia\Gistpen\Params;

use Intraxia\Gistpen\Options\Site;
use Intraxia\Jaxion\Contract\Core\HasFilters;

class Prism implements HasFilters {

	/**
	 * Site options service.
	 *
	 * @var Site
	 */
	private $site;

	/**
	 * Prism constructor.
	 *
	 * @param Site $site
	 */
	public function __construct( Site $site ) {
		$this->site = $site;
	}

	/**
	 * Add prism key to params array.
	 *
	 * @param array $params Current params array.
	 *
	 * @return array
	 */
	public function apply_prism( $params ) {
		$params['prism'] = $this->site->get( 'prism' );

		return $params;
	}
	/**
	 * @inheritDoc
	 */
	public function filter_hooks() {
		return array(
			array(
				'hook'   => 'params.state.content',
				'method' => 'apply_prism',
			),
			array(
				'hook'   => 'params.state.settings',
				'method' => 'apply_prism',
			),
			array(
				'hook'   => 'params.props.settings',
				'method' => 'apply_prism',
			),
			array(
				'hook'   => 'params.props.content.blob',
				'method' => 'apply_prism',
			),
		);
	}
}
