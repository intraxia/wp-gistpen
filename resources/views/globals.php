<?php
/**
 * Renders the application globals.
 *
 * @package Intraxia\Gistpen
 * @var string
 */

return '<script type="application/javascript">window.__GISTPEN_GLOBALS__ = ' . wp_json_encode( $data['globals'] ) . ';</script>';
