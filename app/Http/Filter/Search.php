<?php
namespace Intraxia\Gistpen\Http\Filter;

use Intraxia\Jaxion\Http\Filter;

/**
 * Class Filter\Search
 *
 * @package Intraxia\Gistpen\Http
 * @subpackage Filters
 */
class Search extends Filter {
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct( [
			's' => 'default',
		] );
	}
}
