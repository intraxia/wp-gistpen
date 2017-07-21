<?php
namespace Intraxia\Gistpen\Params;


class Repository {

	/**
	 * Fetches the state for a given page key.
	 *
	 * @param string $key State key to fetch.
	 *
	 * @return array
	 */
	public function state( $key ) {
		return apply_filters( "params.state.{$key}", array() );
	}

	/**
	 * Fetches the props for a given page key.
	 *
	 * @param string $key Props key to fetch.
	 *
	 * @return array
	 */
	public function props( $key ) {
		return apply_filters( "params.props.{$key}", array() );
	}
}
