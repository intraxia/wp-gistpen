<?php
namespace Intraxia\Gistpen\Http\Filter;

use WP_Error;

/**
 * Class Filter\Repo
 *
 * @package Intraxia\Gistpen\Http
 * @subpackage Filters
 */
class RepoUpdate extends RepoCreate {
	/**
	 * Generates argument rules.
	 *
	 * Returns an array matching the WP-API format for argument rules,
	 * including sanitization, validation, required, or defaults.
	 *
	 * @return array
	 */
	public function rules() {
		$rules = parent::rules();

		$rules['id'] = [
			'required' => true,
			'type'     => 'integer',
		];

		return $rules;
	}
}
