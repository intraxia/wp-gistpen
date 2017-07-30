<?php
namespace Intraxia\Gistpen\Params;

use Intraxia\Jaxion\Contract\Core\HasFilters;
use stdClass;

class Route implements HasFilters {

	/**
	 * Add route key to params array for settings page.
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public function apply_settings_route( $params ) {
		$params['route'] = array( 'name' => 'highlighting', 'parts' => $parts = new stdClass );

		return $params;
	}

	/**
	 * Add route key to params array for edit page.
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public function apply_edit_route( $params ) {
		$params['route'] = array(
			'name' => ! empty( $_GET['wpgp_route'] ) ? $_GET['wpgp_route'] : 'editor',

		);

		return $params;
	}

	/**
	 * {@inheritdoc}
	 */
	public function filter_hooks() {
		return array(
			array(
				'hook'     => 'params.state.settings',
				'method'   => 'apply_settings_route',
				'priority' => 5,
			),
			array(
				'hook'     => 'params.props.settings',
				'method'   => 'apply_settings_route',
				'priority' => 5,
			),
			array(
				'hook'     => 'params.state.edit',
				'method'   => 'apply_edit_route',
				'priority' => 5,
			),
			array(
				'hook'     => 'params.props.edit',
				'method'   => 'apply_edit_route',
				'priority' => 5,
			),
		);
	}
}
