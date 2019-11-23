<?php
namespace Intraxia\Gistpen\Params;

/**
 * Service for managing the params.
 */
class Repository {

	/**
	 * Fetches the state for a given page key.
	 *
	 * @param string $key State key to fetch.
	 * @param array  $data Data provided for creating the state.
	 *
	 * @return array
	 */
	public function state( $key, array $data = array() ) {
		return apply_filters( "params.state.{$key}", array(), $data );
	}

	/**
	 * Fetches the props for a given page key.
	 *
	 * @param string $key Props key to fetch.
	 * @param array  $data Data provided for creating the state.
	 *
	 * @return array
	 */
	public function props( $key, array $data = array() ) {
		return apply_filters( "params.props.{$key}", array(), $data );
	}
}
