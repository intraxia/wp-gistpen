<?php
namespace Intraxia\Gistpen\Params;

use Intraxia\Gistpen\Options\Site;
use Intraxia\Jaxion\Contract\Core\HasFilters;

class Gist implements HasFilters {

	/**
	 * Site options service.
	 *
	 * @var Site
	 */
	private $site;

	/**
	 * Gist constructor.
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
	public function apply_gist( $params ) {
		$params['gist'] = $this->site->get( 'gist' );

		return $params;
	}
	/**
	 * @inheritDoc
	 */
	public function filter_hooks() {
		return array(
			array(
				'hook'   => 'params.state.settings',
				'method' => 'apply_gist',
			),
			array(
				'hook'   => 'params.props.settings',
				'method' => 'apply_gist',
			),
		);
	}
}
