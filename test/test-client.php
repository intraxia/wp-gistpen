<?php
wp_set_current_user( 2 );
Requests::set_certificate_path( ABSPATH . WPINC . '/certificates/ca-bundle.crt' );

$client = \Intraxia\Gistpen\App::instance()->fetch('client.gist');

$response = $client->all();

if ( $response instanceof WP_Error ) {
    throw new Exception('response was an error with message: ' . $response->get_error_message() );
}

if ( ! is_array( $response->json ) ) {
    throw new Exception('body was not correctly parsed');
}

if( count( $response->json ) !== 0 ) {
    throw new Exception('all had gists in the response to start');
}

$response = $client->create( array(
    'description' => 'My New Gist',
    'files'       => array(
        'filename.txt' => array(
            'content' => 'File contents',
        ),
    ),
) );

if ( $response instanceof WP_Error ) {
    throw new Exception('response was an error with message: ' . $response->get_error_message() );
}

$response = $client->all();

if ( $response instanceof WP_Error ) {
    throw new Exception('response was an error with message: ' . $response->get_error_message() );
}

if ( ! is_array( $response->json ) ) {
    throw new Exception('body was not correctly parsed');
}

if( count( $response->json ) !== 1 ) {
    throw new Exception('gist was not successfully created');
}

$response = $client->one( $response->json[0]->id );

if ( $response instanceof WP_Error ) {
    throw new Exception('response was an error with message: ' . $response->get_error_message() );
}

$gist = $response->json;

if ( $gist->description !== 'My New Gist' ) {
    throw new Exception( 'description does not match. saw: '. $gist->description );
}

$files = (array) $gist->files;

if ( count( $files ) !== 1 ) {
    throw new Exception( 'Incorrect number of files created' );
}

if ( ! isset( $files['filename.txt'] ) ) {
    throw new Exception( 'file was not created' );
}

if ( $files['filename.txt']->content !== 'File contents' ) {
    throw new Exception( 'file does not have correct content' );
}

$response = $client->update( $gist->id, array(
    'description' => 'New description',
    'files'       => array(
        'filename.txt' => array(
            'filename' => 'new-filename.txt',
            'content'  => 'New file contents',
        ),
    ),
) );

if ( $response instanceof WP_Error ) {
    throw new Exception('response was an error with message: ' . $response->get_error_message() );
}

$response = $client->all();

if ( $response instanceof WP_Error ) {
    throw new Exception('response was an error with message: ' . $response->get_error_message() );
}

if ( ! is_array( $response->json ) ) {
    throw new Exception('body was not correctly parsed');
}

if( count( $response->json ) !== 1 ) {
    throw new Exception('gist was not successfully updated');
}

$response = $client->one( $response->json[0]->id );

if ( $response instanceof WP_Error ) {
    throw new Exception('response was an error with message: ' . $response->get_error_message() );
}

$gist = $response->json;

if ( $gist->description !== 'New description' ) {
    throw new Exception( 'description does not match' );
}

$files = (array) $gist->files;

if ( count( $files ) !== 1 ) {
    throw new Exception( 'Incorrect number of files created' );
}

if ( ! isset( $files['new-filename.txt'] ) ) {
    throw new Exception( 'file was not created' );
}

if ( $files['new-filename.txt']->content !== 'New file contents' ) {
    throw new Exception( 'file does not have correct content' );
}

$response = $client->delete( $gist->id );

if ( $response instanceof WP_Error ) {
    throw new Exception('response was an error with message: ' . $response->get_error_message() );
}

$response = $client->all();

if ( $response instanceof WP_Error ) {
    throw new Exception('response was an error with message: ' . $response->get_error_message() );
}

if ( ! is_array( $response->json ) ) {
    throw new Exception('body was not correctly parsed');
}

if( count( $response->json ) !== 0 ) {
    throw new Exception('gist was not deleted');
}

echo 'Client tests passed successfully!';
