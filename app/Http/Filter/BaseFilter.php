<?php
namespace Intraxia\Gistpen\Http\Filter;

use Intraxia\Jaxion\Contract\Http\Filter as FilterContract;
use WP_Error;

/**
 * Class Filter\BaseFilter
 *
 * @package Intraxia\Gistpen\Http
 * @subpackage Filters
 */
abstract class BaseFilter implements FilterContract {
	/**
	 * Create validation error to return.
	 *
	 * @param  string $message Validation message.
	 * @return WP_Error        Validation error.
	 */
	protected function create_error( $message ) {
		return new WP_Error( 'rest_invalid_param', $message );
	}

	/**
	 * Sanitize the entity with the filter's validation rules
	 *
	 * @param  array  $entity Entity to validate.
	 * @param  string $name   Name property to use for validation.
	 * @return WP_Error       Validation error.
	 */
	protected function sanitize_entity( array $entity, $name = '' ) {
		return rest_validate_value_from_schema( $entity, [
			'type'       => 'object',
			'properties' => $this->rules(),
		], $name );
	}
}
