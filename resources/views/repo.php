<?php
/**
 * Renders the Repo content, looping over its blobs.
 *
 * @package Intraxia\Gistpen
 * @var string
 */

$output = '<div data-brk-container="repo">';

foreach ( $data['repo']['blobs'] as $blob ) {
	$output .= $this->render( 'blob', [ 'blob' => $blob ] );
}

$output .= '</div>';

return $output;
