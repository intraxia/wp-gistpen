<?php
namespace Intraxia\Gistpen\Http\Filter;

use Intraxia\Jaxion\Http\Filter;

/**
 * Class Filter\Search
 *
 * @package Intraxia\Gistpen\Http
 * @subpackage Filters
 */
class Search extends BaseFilter {
	/**
	 * Generates argument rules.
	 *
	 * Returns an array matching the WP-API format for argument rules,
	 * including sanitization, validation, required, or defaults.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			's' => [
				'required'=> false,
				'type' => 'string',
			]
			];
	}
}
